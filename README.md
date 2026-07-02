# BGP Sessions Widget for Zabbix 7.0

[English](#english) | [Português do Brasil](#português-do-brasil)

---

## English

**BGP Sessions** is a custom dashboard widget for **Zabbix 7.0** designed to monitor BGP sessions in a clean and visual way.

The widget automatically discovers BGP items from selected Zabbix host groups and displays each BGP peer as a compact status card.

### Main features

- Visual BGP session cards for Zabbix dashboards.
- Native Zabbix host group selector.
- Automatic discovery of BGP items from the selected host groups.
- Status summary with total sessions, UP, DOWN, STALE, UNKNOWN and received prefixes.
- Compact cards showing peer, host, uptime, received prefixes, remote AS and last check.
- Session state is shown only when the session is not UP, keeping normal/healthy cards cleaner.
- Designed for Juniper BGP item keys currently used by the template.

### Current supported item keys

The current version detects the following item key patterns:

```text
net.bgp.peer.state[AS, PEER]
net.bgp.peer.uptime[AS, PEER]
net.bgp.peer.prefixes.ipv4.received[AS, PEER]
net.bgp.peer.prefixes.ipv6.received[AS, PEER]
```

Example:

```text
net.bgp.peer.state[52863, 10.177.128.86]
net.bgp.peer.uptime[52863, 10.177.128.86]
net.bgp.peer.prefixes.ipv4.received[52863, 10.177.128.86]
```

### Widget configuration

The widget configuration is intentionally simple and follows the Zabbix dashboard style:

| Field | Description |
|---|---|
| Host groups | Zabbix host groups where BGP-enabled routers are located. |
| Consider stale after minutes | Time threshold used to mark a session as stale when item data is too old. |

### Requirements

- Zabbix frontend 7.0.
- BGP items already collected by an existing template.
- A host group containing the routers that expose the BGP items.
- PHP/web server permissions compatible with the Zabbix frontend modules directory.

### Repository structure

```text
.
├── bgp_sessions/              # Zabbix frontend module directory
│   ├── actions/               # Widget controller
│   ├── includes/              # Widget form definition
│   ├── views/                 # Widget edit/view templates
│   ├── assets/css/            # Widget CSS
│   └── manifest.json          # Zabbix module manifest
├── README.md                  # Project documentation
├── INSTALL.md                 # Installation and upgrade guide
├── CHANGELOG.md               # Version history
└── GITHUB_SETUP_WINDOWS11.md  # Initial GitHub publishing guide for Windows 11
```

### License

No license has been defined yet. Before publishing as an open-source project, choose and add a license file, such as MIT, BSD-2-Clause or GPL, according to your preference.

---

## Português do Brasil

**BGP Sessions** é um widget customizado de dashboard para **Zabbix 7.0**, criado para monitorar sessões BGP de forma visual, limpa e objetiva.

O widget descobre automaticamente os itens BGP a partir dos grupos de hosts selecionados no Zabbix e exibe cada peer BGP em um card compacto.

### Principais recursos

- Cards visuais para sessões BGP no dashboard do Zabbix.
- Seletor nativo de grupos de hosts do Zabbix.
- Descoberta automática dos itens BGP a partir dos grupos selecionados.
- Resumo com total de sessões, UP, DOWN, STALE, UNKNOWN e prefixos recebidos.
- Cards compactos com peer, host, uptime, prefixos recebidos, AS remoto e última coleta.
- O estado da sessão aparece somente quando a sessão não está UP, mantendo os cards saudáveis mais limpos.
- Desenvolvido para as keys BGP Juniper utilizadas atualmente pelo template.

### Keys atualmente suportadas

A versão atual detecta automaticamente os seguintes padrões de keys:

```text
net.bgp.peer.state[AS, PEER]
net.bgp.peer.uptime[AS, PEER]
net.bgp.peer.prefixes.ipv4.received[AS, PEER]
net.bgp.peer.prefixes.ipv6.received[AS, PEER]
```

Exemplo:

```text
net.bgp.peer.state[52863, 10.177.128.86]
net.bgp.peer.uptime[52863, 10.177.128.86]
net.bgp.peer.prefixes.ipv4.received[52863, 10.177.128.86]
```

### Configuração do widget

A configuração foi mantida simples e no estilo do dashboard do Zabbix:

| Campo | Descrição |
|---|---|
| Host groups | Grupos de hosts do Zabbix onde estão os roteadores com BGP. |
| Consider stale after minutes | Tempo usado para marcar uma sessão como stale quando os dados dos itens estiverem antigos. |

### Requisitos

- Frontend do Zabbix 7.0.
- Itens BGP já sendo coletados por um template existente.
- Um grupo de hosts contendo os roteadores que possuem os itens BGP.
- Permissões corretas no diretório de módulos do frontend do Zabbix.

### Estrutura do repositório

```text
.
├── bgp_sessions/              # Diretório do módulo frontend do Zabbix
│   ├── actions/               # Controller do widget
│   ├── includes/              # Definição do formulário do widget
│   ├── views/                 # Views de edição e exibição do widget
│   ├── assets/css/            # CSS do widget
│   └── manifest.json          # Manifesto do módulo Zabbix
├── README.md                  # Documentação do projeto
├── INSTALL.md                 # Guia de instalação e atualização
├── CHANGELOG.md               # Histórico de versões
└── GITHUB_SETUP_WINDOWS11.md  # Guia de publicação inicial no GitHub via Windows 11
```

### Licença

Nenhuma licença foi definida ainda. Antes de publicar como projeto open-source, escolha e adicione uma licença, como MIT, BSD-2-Clause ou GPL, conforme sua preferência.
