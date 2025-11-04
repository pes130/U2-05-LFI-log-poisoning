Estructura de web pensada para hacer log poisoning

Pensada para ser desplegada en una máquina virtual.

### Paquetes a instalar:
```bash
sudo apt update
sudo apt install -y apache2 libapache2-mod-php php php-cli php-mbstring php-xml vsftpd acl wget unzip nano
```
Con `libapache2-mod-php` integramos php con apache

### Con apache
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Configuración mínima vsftpd
En /etc/vsftpd.log asegúrate de incluir:
```bash
local_enable=YES
write_enable=YES
```

###
Despliega el repo en /var/www/html

Veamos los permisos:
```bash
sudo chown -R www-data:www-data /var/www/html
```

Establecemos permisos para realizar log poisoning sobre log de ftp y ssh:
```bash
# crear logs si no existen
sudo touch /var/log/auth.log /var/log/vsftpd.log
sudo chown root:adm /var/log/auth.log
sudo chown root:adm /var/log/vsftpd.log
sudo chmod 640 /var/log/auth.log /var/log/vsftpd.log

# aplicar ACLs para lectura por www-data
sudo setfacl -m u:www-data:r /var/log/auth.log /var/log/vsftpd.log

# establecer ACL por defecto para nuevos inodos (no siempre aplicable a /var/log, pero lo ponemos)
sudo setfacl -d -m u:www-data:r /var/log/auth.log /var/log/vsftpd.log || true
```

### Log rotate
Necesitamos que, en caso de que se roten los logs, se conserve la ACL y no perdamos el acceso de lectura para www-data.
Edita el fichero `/etc/logrotate.d/rsyslog` y déjalo así:
```bash
/var/log/syslog
/var/log/mail.log
/var/log/kern.log
/var/log/auth.log
/var/log/user.log
/var/log/cron.log
{
        rotate 4
        weekly
        missingok
        notifempty
        compress
        delaycompress
        sharedscripts
        postrotate
                /usr/lib/rsyslog/rsyslog-rotate
                /usr/bin/setfacl -m u:www-data:r /var/log/auth.log >/dev/null 2>&1 || true
        endscript
}
```
Y si tienes /etc/logrotate.d/vsftpd:
```bash                                          
/var/log/vsftpd.log
{
    create 640 root adm

    # ftpd doesn't handle SIGHUP properly
    missingok
    notifempty
    rotate 4
    weekly

    postrotate
        # Reaplicar ACL para que www-data pueda leer el nuevo log
        /usr/bin/setfacl -m u:www-data:r /var/log/vsftpd.log >/dev/null 2>&1 || true
    endscript
}

```