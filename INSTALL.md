# Installation Guide / Guia de Instalação

[English](#english) | [Português do Brasil](#português-do-brasil)

---

## English

This guide explains how to install or update the **BGP Sessions** widget in a Zabbix 7.0 frontend.

### 1. Copy the module to the Zabbix frontend server

Clone the repository or copy the project files to the server where the Zabbix web frontend is installed.

Example using Git:

```bash
git clone https://github.com/YOUR_GITHUB_USER/zabbix-bgp-sessions-widget.git
cd zabbix-bgp-sessions-widget
```

### 2. Install the module

The module directory must be copied to the Zabbix frontend `modules` directory.

Common Zabbix frontend path on Debian/Ubuntu:

```bash
/usr/share/zabbix/modules
```

Install or update the module:

```bash
sudo mkdir -p /usr/share/zabbix/modules
sudo rsync -a --delete bgp_sessions/ /usr/share/zabbix/modules/bgp_sessions/
```

### 3. Fix permissions

Debian/Ubuntu with Apache:

```bash
sudo chown -R root:www-data /usr/share/zabbix/modules/bgp_sessions
sudo find /usr/share/zabbix/modules/bgp_sessions -type d -exec chmod 755 {} \;
sudo find /usr/share/zabbix/modules/bgp_sessions -type f -exec chmod 644 {} \;
```

RHEL/Oracle Linux/CentOS with Apache:

```bash
sudo chown -R root:apache /usr/share/zabbix/modules/bgp_sessions
sudo find /usr/share/zabbix/modules/bgp_sessions -type d -exec chmod 755 {} \;
sudo find /usr/share/zabbix/modules/bgp_sessions -type f -exec chmod 644 {} \;
```

### 4. Validate PHP syntax

```bash
php -l /usr/share/zabbix/modules/bgp_sessions/actions/WidgetView.php
php -l /usr/share/zabbix/modules/bgp_sessions/includes/WidgetForm.php
php -l /usr/share/zabbix/modules/bgp_sessions/views/widget.edit.php
php -l /usr/share/zabbix/modules/bgp_sessions/views/widget.view.php
```

Expected result:

```text
No syntax errors detected
```

### 5. Restart the web frontend

Apache on Debian/Ubuntu:

```bash
sudo systemctl restart apache2
```

Apache on RHEL/Oracle Linux/CentOS:

```bash
sudo systemctl restart httpd
```

If PHP-FPM is used:

```bash
sudo systemctl restart php*-fpm
```

### 6. Enable the module in Zabbix

In the Zabbix frontend:

```text
Administration → General → Modules → Scan directory
```

Enable:

```text
BGP Sessions
```

Expected module information:

```text
Name: BGP Sessions
Version: 0.3.5
Author: Net Tech
Status: Enabled
```

### 7. Add the widget to a dashboard

Open a dashboard and select:

```text
Edit dashboard → Add widget → BGP Sessions
```

Configure:

| Field | Recommended value |
|---|---|
| Host groups | Group where the BGP routers are located. |
| Consider stale after minutes | 15 |

Save the dashboard.

### 8. Browser cache

If the widget appears without styling after an update, refresh the browser cache:

```text
Ctrl + F5
```

If needed, restart Apache/PHP-FPM again.

---

## Português do Brasil

Este guia explica como instalar ou atualizar o widget **BGP Sessions** no frontend do Zabbix 7.0.

### 1. Copiar o módulo para o servidor do frontend Zabbix

Clone o repositório ou copie os arquivos do projeto para o servidor onde está instalado o frontend web do Zabbix.

Exemplo usando Git:

```bash
git clone https://github.com/SEU_USUARIO_GITHUB/zabbix-bgp-sessions-widget.git
cd zabbix-bgp-sessions-widget
```

### 2. Instalar o módulo

O diretório do módulo precisa ser copiado para o diretório `modules` do frontend do Zabbix.

Caminho comum em Debian/Ubuntu:

```bash
/usr/share/zabbix/modules
```

Instale ou atualize o módulo:

```bash
sudo mkdir -p /usr/share/zabbix/modules
sudo rsync -a --delete bgp_sessions/ /usr/share/zabbix/modules/bgp_sessions/
```

### 3. Ajustar permissões

Debian/Ubuntu com Apache:

```bash
sudo chown -R root:www-data /usr/share/zabbix/modules/bgp_sessions
sudo find /usr/share/zabbix/modules/bgp_sessions -type d -exec chmod 755 {} \;
sudo find /usr/share/zabbix/modules/bgp_sessions -type f -exec chmod 644 {} \;
```

RHEL/Oracle Linux/CentOS com Apache:

```bash
sudo chown -R root:apache /usr/share/zabbix/modules/bgp_sessions
sudo find /usr/share/zabbix/modules/bgp_sessions -type d -exec chmod 755 {} \;
sudo find /usr/share/zabbix/modules/bgp_sessions -type f -exec chmod 644 {} \;
```

### 4. Validar sintaxe PHP

```bash
php -l /usr/share/zabbix/modules/bgp_sessions/actions/WidgetView.php
php -l /usr/share/zabbix/modules/bgp_sessions/includes/WidgetForm.php
php -l /usr/share/zabbix/modules/bgp_sessions/views/widget.edit.php
php -l /usr/share/zabbix/modules/bgp_sessions/views/widget.view.php
```

Resultado esperado:

```text
No syntax errors detected
```

### 5. Reiniciar o frontend web

Apache em Debian/Ubuntu:

```bash
sudo systemctl restart apache2
```

Apache em RHEL/Oracle Linux/CentOS:

```bash
sudo systemctl restart httpd
```

Se usar PHP-FPM:

```bash
sudo systemctl restart php*-fpm
```

### 6. Habilitar o módulo no Zabbix

No frontend do Zabbix:

```text
Administration → General → Modules → Scan directory
```

Habilite:

```text
BGP Sessions
```

Informação esperada do módulo:

```text
Name: BGP Sessions
Version: 0.3.5
Author: Net Tech
Status: Enabled
```

### 7. Adicionar o widget ao dashboard

Abra o dashboard e acesse:

```text
Edit dashboard → Add widget → BGP Sessions
```

Configure:

| Campo | Valor recomendado |
|---|---|
| Host groups | Grupo onde estão os roteadores BGP. |
| Consider stale after minutes | 15 |

Salve o dashboard.

### 8. Cache do navegador

Se o widget aparecer sem formatação após uma atualização, force a atualização do cache do navegador:

```text
Ctrl + F5
```

Se necessário, reinicie novamente Apache/PHP-FPM.
