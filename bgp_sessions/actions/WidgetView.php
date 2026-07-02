<?php

namespace Modules\BgpSessions\Actions;

use API;
use CControllerDashboardWidgetView;
use CControllerResponseData;

/**
 * Presentation controller for BGP Sessions widget.
 */
class WidgetView extends CControllerDashboardWidgetView {

    private const BGP_STATES = [
        0 => 'Unknown',
        1 => 'Idle',
        2 => 'Connect',
        3 => 'Active',
        4 => 'OpenSent',
        5 => 'OpenConfirm',
        6 => 'Established'
    ];

    /**
     * Keys used by the Juniper BGP template sent by the user.
     * The widget also has a generic fallback using item names and tags.
     */
    private const BGP_KEY_SEARCHES = [
        'net.bgp.peer.state',
        'net.bgp.peer.uptime',
        'net.bgp.peer.prefixes.ipv4.received',
        'net.bgp.peer.prefixes.ipv6.received',
        'net.bgp.peer.prefixes',
        'net.bgp.peer'
    ];

    protected function doAction(): void {
        $groupids = $this->normalizeIds($this->fields_values['groupids'] ?? []);
        $stale_after = max(1, (int) ($this->fields_values['stale_after'] ?? 15));

        $result = [
            'name' => $this->getInput('name', $this->widget->getName()),
            'peers' => [],
            'summary' => [
                'total' => 0,
                'up' => 0,
                'down' => 0,
                'stale' => 0,
                'unknown' => 0,
                'routes_total' => 0
            ],
            'messages' => [],
            'debug' => [
                'items_found' => 0,
                'queries' => self::BGP_KEY_SEARCHES
            ],
            'config' => [
                'groupids' => $groupids,
                'stale_after' => $stale_after
            ],
            'user' => [
                'debug_mode' => $this->getDebugMode()
            ]
        ];

        if (!$groupids) {
            $result['messages'][] = _('Select at least one host group.');
            $this->setResponse(new CControllerResponseData($result));
            return;
        }

        $groups = API::HostGroup()->get([
            'output' => ['groupid', 'name'],
            'groupids' => $groupids,
            'preservekeys' => true
        ]);

        if (!$groups) {
            $result['messages'][] = _('Selected host group was not found or is not accessible.');
            $this->setResponse(new CControllerResponseData($result));
            return;
        }

        $hosts = API::Host()->get([
            'output' => ['hostid', 'host', 'name'],
            'groupids' => array_keys($groups),
            'monitored_hosts' => true,
            'preservekeys' => true
        ]);

        if (!$hosts) {
            $result['messages'][] = _('No monitored hosts found in the selected host groups.');
            $this->setResponse(new CControllerResponseData($result));
            return;
        }

        $items = $this->getBgpCandidateItems(array_keys($hosts));
        $result['debug']['items_found'] = count($items);

        if (!$items) {
            $result['messages'][] = _('No BGP items were found in the selected host groups.');
            $result['messages'][] = 'The widget searches enabled items with keys starting with net.bgp.peer, matching the Juniper BGP template.';
            $result['messages'][] = 'Confirm that the router host is in the selected group and that discovered BGP items exist in Latest data.';
            $this->setResponse(new CControllerResponseData($result));
            return;
        }

        $sessions = $this->buildSessions($items);

        if (!$sessions) {
            $result['messages'][] = _('BGP items were found, but no session state, uptime or received-prefix metric could be identified.');
            $result['messages'][] = 'Expected keys: net.bgp.peer.state[], net.bgp.peer.uptime[] and net.bgp.peer.prefixes.*.received[].';
            $this->setResponse(new CControllerResponseData($result));
            return;
        }

        $now = time();
        $stale_seconds = $stale_after * 60;
        $peers = [];

        foreach ($sessions as $metrics) {
            $state_item = $metrics['state'] ?? null;
            $uptime_item = $metrics['uptime'] ?? null;
            $routes_items = $metrics['routes_items'] ?? [];

            $base_item = $state_item ?? $uptime_item ?? ($routes_items[0] ?? null);
            if ($base_item === null) {
                continue;
            }

            $hostid = (string) $base_item['hostid'];
            if (!array_key_exists($hostid, $hosts)) {
                continue;
            }

            $host = $hosts[$hostid]['name'] ?: $hosts[$hostid]['host'];
            $peer_id = $metrics['_peer'] ?? $this->extractPeerId($base_item);
            $remote_as = $metrics['_remote_as'] ?? $this->extractRemoteAs($base_item);

            $state_value = $state_item !== null ? $this->normalizeState($state_item['lastvalue']) : 0;
            $lastclock = $this->latestClock(array_merge([$state_item, $uptime_item], $routes_items));
            $state_clock = $state_item !== null ? (int) ($state_item['lastclock'] ?? 0) : 0;
            $is_stale = ($lastclock === 0 || ($now - $lastclock) > $stale_seconds || ($state_clock > 0 && ($now - $state_clock) > $stale_seconds));
            $is_up = ($state_value === 6 && !$is_stale);

            $routes_value = $this->sumRoutes($routes_items);
            if ($routes_value !== null) {
                $result['summary']['routes_total'] += $routes_value;
            }

            $state_label = self::BGP_STATES[$state_value] ?? 'Unknown';
            $status = $is_stale ? 'stale' : ($is_up ? 'up' : ($state_value === 0 ? 'unknown' : 'down'));

            $peer = [
                'host' => $host,
                'peer' => $peer_id,
                'remote_as' => $remote_as !== '' ? $remote_as : '—',
                'state_value' => $state_value,
                'state_label' => $state_label,
                'status' => $status,
                'status_label' => strtoupper($status),
                'up' => $is_up,
                'stale' => $is_stale,
                'last_seen' => $this->formatAge($lastclock, $now),
                'last_clock' => $lastclock,
                'uptime' => $uptime_item !== null && is_numeric($uptime_item['lastvalue'])
                    ? $this->formatSeconds((int) $uptime_item['lastvalue'])
                    : '—',
                'routes' => $routes_value !== null ? number_format($routes_value, 0, '.', ',') : '—',
                'routes_raw' => $routes_value,
                'item_count' => $this->countDetectedItems($metrics)
            ];

            if ($status === 'stale') {
                $result['summary']['stale']++;
            }
            elseif ($status === 'up') {
                $result['summary']['up']++;
            }
            elseif ($status === 'unknown') {
                $result['summary']['unknown']++;
            }
            else {
                $result['summary']['down']++;
            }

            $peers[] = $peer;
        }

        usort($peers, static function (array $a, array $b): int {
            $rank = ['down' => 0, 'stale' => 1, 'unknown' => 2, 'up' => 3];
            $ra = $rank[$a['status']] ?? 9;
            $rb = $rank[$b['status']] ?? 9;

            if ($ra !== $rb) {
                return $ra <=> $rb;
            }

            return strnatcasecmp($a['host'].' '.$a['peer'], $b['host'].' '.$b['peer']);
        });

        $result['peers'] = $peers;
        $result['summary']['total'] = count($peers);

        $this->setResponse(new CControllerResponseData($result));
    }

    private function normalizeIds($value): array {
        if ($value === null || $value === '') {
            return [];
        }

        if (!is_array($value)) {
            return [(string) $value];
        }

        $ids = [];
        foreach ($value as $entry) {
            if (is_array($entry)) {
                foreach (['groupid', 'id', 'value'] as $key) {
                    if (array_key_exists($key, $entry) && $entry[$key] !== '') {
                        $ids[] = (string) $entry[$key];
                        continue 2;
                    }
                }
            }
            elseif ($entry !== '') {
                $ids[] = (string) $entry;
            }
        }

        return array_values(array_unique($ids));
    }

    private function getBgpCandidateItems(array $hostids): array {
        $items_by_id = [];

        foreach (self::BGP_KEY_SEARCHES as $query) {
            $items = API::Item()->get([
                'output' => ['itemid', 'hostid', 'name', 'key_', 'lastvalue', 'lastclock', 'value_type', 'units', 'state', 'status', 'error'],
                'selectTags' => ['tag', 'value'],
                'hostids' => $hostids,
                'filter' => ['status' => '0'],
                'search' => ['key_' => $query],
                'searchByAny' => true,
                'searchWildcardsEnabled' => false
            ]) ?: [];

            foreach ($items as $item) {
                $items_by_id[(string) $item['itemid']] = $item;
            }
        }

        if (!$items_by_id) {
            $items = API::Item()->get([
                'output' => ['itemid', 'hostid', 'name', 'key_', 'lastvalue', 'lastclock', 'value_type', 'units', 'state', 'status', 'error'],
                'selectTags' => ['tag', 'value'],
                'hostids' => $hostids,
                'filter' => ['status' => '0'],
                'search' => ['name' => 'BGP'],
                'searchByAny' => true,
                'searchWildcardsEnabled' => false
            ]) ?: [];

            foreach ($items as $item) {
                $items_by_id[(string) $item['itemid']] = $item;
            }
        }

        $items = array_values($items_by_id);

        usort($items, static function (array $a, array $b): int {
            return strnatcasecmp(
                ((string) ($a['hostid'] ?? '')).' '.((string) ($a['key_'] ?? '')).' '.((string) ($a['name'] ?? '')),
                ((string) ($b['hostid'] ?? '')).' '.((string) ($b['key_'] ?? '')).' '.((string) ($b['name'] ?? ''))
            );
        });

        return $items;
    }

    private function buildSessions(array $items): array {
        $sessions = [];

        foreach ($items as $item) {
            $metric = $this->detectMetric($item);
            if ($metric === null) {
                continue;
            }

            $peer_id = $this->extractPeerId($item);
            $remote_as = $this->extractRemoteAs($item);
            $idx = (string) $item['hostid'].'|'.$remote_as.'|'.$peer_id;

            if (!array_key_exists($idx, $sessions)) {
                $sessions[$idx] = [
                    '_peer' => $peer_id,
                    '_remote_as' => $remote_as
                ];
            }

            if ($peer_id !== '') {
                $sessions[$idx]['_peer'] = $peer_id;
            }
            if ($remote_as !== '') {
                $sessions[$idx]['_remote_as'] = $remote_as;
            }

            if ($metric === 'routes') {
                if (!array_key_exists('routes_items', $sessions[$idx])) {
                    $sessions[$idx]['routes_items'] = [];
                }
                $sessions[$idx]['routes_items'][(string) $item['itemid']] = $item;
                continue;
            }

            if (!array_key_exists($metric, $sessions[$idx]) || $this->isBetterItem($metric, $item, $sessions[$idx][$metric])) {
                $sessions[$idx][$metric] = $item;
            }
        }

        return $sessions;
    }

    private function detectMetric(array $item): ?string {
        $key = strtolower((string) ($item['key_'] ?? ''));
        $name = strtolower((string) ($item['name'] ?? ''));
        $text = $name.' '.$key;
        $compact = preg_replace('/[^a-z0-9]+/', '', $text);

        if (strpos($key, 'net.bgp.peer.state[') !== false) {
            return 'state';
        }

        if (strpos($key, 'net.bgp.peer.uptime[') !== false) {
            return 'uptime';
        }

        if (preg_match('/net\.bgp\.peer\.prefixes\.(ipv4|ipv6)\.received\[/', $key)) {
            return 'routes';
        }

        if (strpos($key, 'net.bgp.peer.status[') !== false) {
            return null;
        }

        if (strpos($text, 'bgp') === false && strpos($compact, 'bgp') === false) {
            return null;
        }

        if (strpos($compact, 'peerstate') !== false || preg_match('/\bpeer\s+state\b/', $name)) {
            return 'state';
        }

        if (strpos($compact, 'establishedtime') !== false || preg_match('/\b(established\s*time|uptime|up\s*time|connection\s*time)\b/', $text)) {
            return 'uptime';
        }

        if ((preg_match('/\b(prefix|prefixes|routes|route)\b/', $text)
                && preg_match('/\b(received|receive|in|input)\b/', $text)
                && !preg_match('/\b(accepted|rejected|denied|advertised|advertis|sent|out|output)\b/', $text))) {
            return 'routes';
        }

        return null;
    }

    private function normalizeTags(array $tags): array {
        $normalized = [];
        foreach ($tags as $tag) {
            if (is_array($tag) && array_key_exists('tag', $tag)) {
                $tag_name = strtolower((string) $tag['tag']);
                $tag_value = trim((string) ($tag['value'] ?? ''));

                if (!array_key_exists($tag_name, $normalized)) {
                    $normalized[$tag_name] = $tag_value;
                }
            }
        }
        return $normalized;
    }

    private function extractPeerId(array $item): string {
        $tags = $this->normalizeTags($item['tags'] ?? []);
        foreach (['address', 'peer', 'neighbor', 'ip', 'remote_address', 'bgp_peer', 'bgp.peer', 'remote_peer'] as $tag) {
            if (array_key_exists($tag, $tags) && $tags[$tag] !== '') {
                return (string) $tags[$tag];
            }
        }

        $parts = $this->extractKeyArguments((string) ($item['key_'] ?? ''));
        if (count($parts) >= 2) {
            return $parts[1];
        }
        if (count($parts) === 1 && $parts[0] !== '') {
            return $parts[0];
        }

        $text = (string) ($item['name'] ?? '').' '.(string) ($item['key_'] ?? '');
        if (preg_match('/(?<![\d.])((?:\d{1,3}\.){3}\d{1,3})(?![\d.])/', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/\b([a-f0-9]{0,4}:){2,}[a-f0-9]{0,4}\b/i', $text, $matches)) {
            return $matches[0];
        }

        return (string) ($item['key_'] ?? $item['itemid'] ?? 'unknown');
    }

    private function extractRemoteAs(array $item): string {
        $tags = $this->normalizeTags($item['tags'] ?? []);
        foreach (['as', 'asn', 'remote_as', 'remote-as', 'peer_as', 'peer-as', 'as_number'] as $tag) {
            if (array_key_exists($tag, $tags) && $tags[$tag] !== '') {
                return (string) $tags[$tag];
            }
        }

        $parts = $this->extractKeyArguments((string) ($item['key_'] ?? ''));
        if (count($parts) >= 1 && preg_match('/^\d+$/', $parts[0])) {
            return $parts[0];
        }

        $name = (string) ($item['name'] ?? '');
        if (preg_match('/\bAS\s*\[?(\d+)\]?/i', $name, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function extractKeyArguments(string $key): array {
        if (!preg_match('/\[(.*)\]/', $key, $matches)) {
            return [];
        }

        $inside = trim($matches[1]);
        if ($inside === '') {
            return [];
        }

        return array_values(array_filter(array_map(static function ($part): string {
            return trim((string) $part, " \t\n\r\0\x0B\"'");
        }, str_getcsv($inside)), static function ($part): bool {
            return $part !== '';
        }));
    }

    private function isBetterItem(string $metric, array $candidate, array $current): bool {
        $candidate_clock = (int) ($candidate['lastclock'] ?? 0);
        $current_clock = (int) ($current['lastclock'] ?? 0);

        if ($candidate_clock !== $current_clock) {
            return $candidate_clock > $current_clock;
        }

        return strlen((string) ($candidate['key_'] ?? '')) < strlen((string) ($current['key_'] ?? ''));
    }

    private function normalizeState($value): int {
        if (is_numeric($value)) {
            $state = (int) $value;
            return array_key_exists($state, self::BGP_STATES) ? $state : 0;
        }

        $text = strtolower(trim((string) $value));
        if ($text === '') {
            return 0;
        }
        if (strpos($text, 'established') !== false || $text === 'up' || $text === 'ok') {
            return 6;
        }
        if (strpos($text, 'openconfirm') !== false) {
            return 5;
        }
        if (strpos($text, 'opensent') !== false) {
            return 4;
        }
        if (strpos($text, 'active') !== false) {
            return 3;
        }
        if (strpos($text, 'connect') !== false) {
            return 2;
        }
        if (strpos($text, 'idle') !== false || strpos($text, 'down') !== false) {
            return 1;
        }

        return 0;
    }

    private function sumRoutes(array $items): ?int {
        $total = 0;
        $found = false;

        foreach ($items as $item) {
            if (is_numeric($item['lastvalue'])) {
                $total += (int) $item['lastvalue'];
                $found = true;
            }
        }

        return $found ? $total : null;
    }

    private function latestClock(array $items): int {
        $latest = 0;
        foreach ($items as $item) {
            if ($item !== null) {
                $latest = max($latest, (int) ($item['lastclock'] ?? 0));
            }
        }
        return $latest;
    }

    private function countDetectedItems(array $metrics): int {
        $count = 0;
        foreach (['state', 'uptime'] as $metric) {
            if (array_key_exists($metric, $metrics)) {
                $count++;
            }
        }
        $count += count($metrics['routes_items'] ?? []);
        return $count;
    }

    private function formatSeconds(int $seconds): string {
        if ($seconds <= 0) {
            return '—';
        }

        $days = intdiv($seconds, 86400);
        $seconds %= 86400;
        $hours = intdiv($seconds, 3600);
        $seconds %= 3600;
        $minutes = intdiv($seconds, 60);

        if ($days > 0) {
            return sprintf('%dd %02dh %02dm', $days, $hours, $minutes);
        }

        return sprintf('%02dh %02dm', $hours, $minutes);
    }

    private function formatAge(int $clock, int $now): string {
        if ($clock <= 0) {
            return 'no data';
        }

        $age = max(0, $now - $clock);
        if ($age < 60) {
            return $age.'s ago';
        }
        if ($age < 3600) {
            return intdiv($age, 60).'m ago';
        }
        if ($age < 86400) {
            return intdiv($age, 3600).'h ago';
        }

        return intdiv($age, 86400).'d ago';
    }
}
