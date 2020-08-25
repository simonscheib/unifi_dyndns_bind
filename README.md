# tl; dr
Little PHP script to 'simulate' a dyndns.org like Dynamic DNS service hosted on your own BIND DNS resolver. 
Works for example for UniFi Product line of gateways. 

works for me :). Feel free to debug and adjust if needed.

# Setup

## Install Packages
```
apt install bind9 bind9-utils dnsutils
```

## Create a Key for nsupdate
1. 
    ```
    dnssec-keygen -a HMAC-SHA512 -b 512 -n USER -r /dev/urandom meinkey
    ```
2. Copy secret in `*.key` file 

## Create BIND configs
1. Create `/var/lib/bind/keys.conf` with `chmod 600` and contents:
    ```
    key "ddns-key" {
        algorithm "HMAC-SHA512";
        secret "COPIED SECRET FROM ABOVE";
    };
    ```
2. Append `include "/var/lib/bind/keys.conf";` to `named.conf`
3. Define zone file in `named.conf.local`
    ```
    zone "dyn.example.com" {
        type master;
        file "/var/lib/bind/db.dyn.example.com";
        allow-update { key ddns-key; };
    };
    ```
4. Create basic zone file
    ```
    cp /etc/bind/db.empty /var/lib/bind/db.dyn.example.com
    ```
5. Adjust SOA and NS values to your needs.
6. Restart BIND
7. Set NS and GLUE Records at your primary DNS provider to point `dyn.example.com` to your BIND server

## Setup PHP backend
**All paths are suited for an ISPCOnfig installation**
1. Copy update.php to `/var/www/example.com/web/nic`
1. Copy config.php and functions.php to `/var/www/example.com/private`
1. Adjust Variables in `config.php`
1. Create `.htpasswd` 
    ```
    htpasswd -c ../../private/.htpasswd ddns
    ```
1. Create `.htaccess`

    Rewrites update.php to be served via /nic/update without file extension. (Needed for UniFi)
    ```
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^([^\.]+)$ $1.php [NC,L]

    <Files .htaccess>
    Order allow,deny
    Deny from all
    </Files>

    <FilesMatch "update\.php$">
    AuthType Basic
    AuthName DynDNS
    AuthUserFile "/var/www/example.com/private/.htpasswd"
    Require user ddns
    </FilesMatch>
    ```
