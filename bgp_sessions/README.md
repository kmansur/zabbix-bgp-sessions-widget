# BGP Sessions Widget for Zabbix 7.0

Version 0.3.4

Custom Zabbix dashboard widget for BGP session monitoring.

## Changes in 0.3.4

- Keeps the compact card layout.
- Hides `State: Established` when the session status is `UP`.
- Shows the state line only for non-UP sessions, such as `DOWN`, `STALE` or `UNKNOWN`.
- Keeps automatic Juniper BGP item discovery.
- CSS file renamed to `widget_v034.css` to avoid browser/module cache.

## Supported item keys

- `net.bgp.peer.state[AS, PEER]`
- `net.bgp.peer.uptime[AS, PEER]`
- `net.bgp.peer.prefixes.ipv4.received[AS, PEER]`
- `net.bgp.peer.prefixes.ipv6.received[AS, PEER]`

## Installation

Copy `bgp_sessions` to your Zabbix frontend modules directory and scan modules in the Zabbix frontend.
