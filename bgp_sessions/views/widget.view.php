<?php

/**
 * BGP Sessions widget card presentation view.
 *
 * @var CView $this
 * @var array $data
 */

if (!function_exists('bgp_sessions_el')) {
    function bgp_sessions_el(string $tag, string $class = '', $items = null): CTag {
        $element = new CTag($tag, true, $items);

        if ($class !== '') {
            $element->addClass($class);
        }

        return $element;
    }
}

if (!function_exists('bgp_sessions_metric')) {
    function bgp_sessions_metric(string $label, $value, string $class = ''): CTag {
        return bgp_sessions_el('div', 'bgp-card-metric '.$class, [
            bgp_sessions_el('span', 'bgp-card-metric-label', $label),
            bgp_sessions_el('strong', 'bgp-card-metric-value', (string) $value)
        ]);
    }
}

if (!function_exists('bgp_sessions_kpi')) {
    function bgp_sessions_kpi(string $label, $value, string $class = ''): CTag {
        return bgp_sessions_el('div', 'bgp-kpi '.$class, [
            bgp_sessions_el('span', '', $label),
            bgp_sessions_el('strong', '', (string) $value)
        ]);
    }
}

$summary = $data['summary'];
$messages = $data['messages'] ?? [];
$cards = [];

foreach ($data['peers'] as $peer) {
    $status_class = 'is-'.$peer['status'];

    $card_items = [
        bgp_sessions_el('div', 'bgp-card-header', [
            bgp_sessions_el('div', 'bgp-peer-block', [
                bgp_sessions_el('div', 'bgp-peer-address', $peer['peer']),
                bgp_sessions_el('div', 'bgp-peer-host', $peer['host'])
            ]),
            bgp_sessions_el('div', 'bgp-status-pill '.$status_class, [
                bgp_sessions_el('span', 'bgp-status-dot '.$status_class, ''),
                bgp_sessions_el('span', '', $peer['status_label'])
            ])
        ])
    ];

    if ($peer['status'] !== 'up') {
        $card_items[] = bgp_sessions_el('div', 'bgp-state-line', [
            bgp_sessions_el('span', '', 'State:'),
            bgp_sessions_el('strong', '', $peer['state_label'])
        ]);
    }

    $card_items[] = bgp_sessions_el('div', 'bgp-card-grid', [
        bgp_sessions_metric('Uptime', $peer['uptime']),
        bgp_sessions_metric('Received prefixes', $peer['routes']),
        bgp_sessions_metric('Remote AS', $peer['remote_as']),
        bgp_sessions_metric('Last check', $peer['last_seen'])
    ]);

    $cards[] = bgp_sessions_el('div', 'bgp-session-card '.$status_class, $card_items);
}

$content = [
    bgp_sessions_el('div', 'bgp-kpi-row', [
        bgp_sessions_kpi('Total', $summary['total']),
        bgp_sessions_kpi('UP', $summary['up'], 'is-up'),
        bgp_sessions_kpi('DOWN', $summary['down'], 'is-down'),
        bgp_sessions_kpi('Stale', $summary['stale'], 'is-stale'),
        bgp_sessions_kpi('Unknown', $summary['unknown'] ?? 0, 'is-unknown'),
        bgp_sessions_kpi('Received prefixes', number_format((int) $summary['routes_total'], 0, '.', ','))
    ])
];

if ($messages) {
    $message_items = [];
    foreach ($messages as $message) {
        $message_items[] = bgp_sessions_el('li', '', $message);
    }

    $content[] = bgp_sessions_el('div', 'bgp-message', [
        bgp_sessions_el('ul', '', $message_items)
    ]);
}

if ($cards) {
    $content[] = bgp_sessions_el('div', 'bgp-card-layout', $cards);
}
else {
    $content[] = bgp_sessions_el('div', 'bgp-empty', _('No BGP sessions found for the configured host groups.'));
}

(new CWidgetView($data))
    ->addItem(bgp_sessions_el('div', 'bgp-sessions-wrapper', $content))
    ->show();
