# Changelog / Histórico de Alterações

[English](#english) | [Português do Brasil](#português-do-brasil)

---

## English

All notable changes to this project are documented in this file.

The format follows the idea of keeping clear, human-readable release notes.

### [0.3.5] - 2026-07-02

#### Changed

- Replaced internal-looking README examples with generic placeholder values.
- Renamed the CSS asset to `widget.css` so the filename is not tied to a specific module version.
- Updated the module manifest to reference the generic CSS asset name.

### [0.3.4] - 2026-07-02

#### Changed

- Kept the compact card layout introduced in the previous versions.
- Hid the `State: Established` line when the BGP session is UP.
- The state line is now shown only when the session is not UP, such as DOWN, STALE or UNKNOWN.
- Kept automatic discovery for the Juniper BGP item key pattern.

### [0.3.3] - 2026-07-02

#### Changed

- Removed the `Detected items` line from the BGP cards.
- Reduced card height to make the dashboard more compact.

### [0.3.2] - 2026-07-02

#### Fixed

- Fixed CSS loading/cache issue that caused cards to appear as unstyled plain text.
- Added a dedicated CSS asset for the corrected card layout.

### [0.3.1] - 2026-07-02

#### Changed

- Switched the visual presentation back from table layout to compact cards.
- Kept the automatic BGP item discovery logic from version 0.3.0.

### [0.3.0] - 2026-07-02

#### Changed

- Removed manual item prefix fields from the widget configuration.
- Added automatic item discovery based on the current Juniper BGP template keys.
- Kept only native Zabbix-style configuration fields:
  - Host groups
  - Consider stale after minutes

### [0.2.1] - 2026-07-02

#### Fixed

- Removed unsupported API sorting by `hostid`.
- Added support for extracting Remote AS and Peer IP from keys using the pattern `net.bgp.peer.*[AS, PEER]`.

### [0.2.0] - 2026-07-02

#### Changed

- Replaced fixed host group input with the native Zabbix host group selector.
- Started moving away from manual item key prefixes.

### [0.1.1] - 2026-07-02

#### Changed

- Renamed the widget from `Net Tech BGP Sessions` to `BGP Sessions`.
- Standardized visible text in English.

### [0.1.0] - 2026-07-02

#### Added

- Initial MVP custom dashboard widget for Zabbix 7.0.
- BGP session cards with status, uptime, routes, remote AS and last check.

---

## Português do Brasil

Todas as alterações relevantes deste projeto estão documentadas neste arquivo.

O formato segue a ideia de manter notas de versão claras e fáceis de entender.

### [0.3.5] - 2026-07-02

#### Alterado

- Substituídos os exemplos do README que pareciam informações internas por valores genéricos.
- Renomeado o asset CSS para `widget.css`, evitando vincular o nome do arquivo a uma versão específica do módulo.
- Atualizado o manifesto do módulo para referenciar o nome genérico do CSS.

### [0.3.4] - 2026-07-02

#### Alterado

- Mantido o layout compacto em cards das versões anteriores.
- Removida a linha `State: Established` quando a sessão BGP está UP.
- A linha de estado agora aparece somente quando a sessão não está UP, como DOWN, STALE ou UNKNOWN.
- Mantida a descoberta automática para o padrão de keys BGP Juniper.

### [0.3.3] - 2026-07-02

#### Alterado

- Removida a linha `Detected items` dos cards BGP.
- Reduzida a altura dos cards para deixar o dashboard mais compacto.

### [0.3.2] - 2026-07-02

#### Corrigido

- Corrigido problema de carregamento/cache do CSS que fazia os cards aparecerem como texto puro sem formatação.
- Adicionado asset CSS dedicado para o layout corrigido em cards.

### [0.3.1] - 2026-07-02

#### Alterado

- Retorno da apresentação visual de tabela para cards compactos.
- Mantida a lógica de descoberta automática de itens BGP da versão 0.3.0.

### [0.3.0] - 2026-07-02

#### Alterado

- Removidos os campos manuais de prefixo de key da configuração do widget.
- Adicionada descoberta automática de itens baseada nas keys do template BGP Juniper atual.
- Mantidos apenas campos de configuração no estilo nativo do Zabbix:
  - Host groups
  - Consider stale after minutes

### [0.2.1] - 2026-07-02

#### Corrigido

- Removida ordenação não suportada pela API usando `hostid`.
- Adicionado suporte para extrair Remote AS e Peer IP das keys no padrão `net.bgp.peer.*[AS, PEER]`.

### [0.2.0] - 2026-07-02

#### Alterado

- Substituído campo fixo de grupo pelo seletor nativo de grupos do Zabbix.
- Iniciada a remoção da dependência de prefixos manuais de keys.

### [0.1.1] - 2026-07-02

#### Alterado

- Renomeado o widget de `Net Tech BGP Sessions` para `BGP Sessions`.
- Padronizados os textos visíveis em inglês.

### [0.1.0] - 2026-07-02

#### Adicionado

- MVP inicial do widget customizado para dashboard do Zabbix 7.0.
- Cards de sessão BGP com status, uptime, rotas, AS remoto e última coleta.
