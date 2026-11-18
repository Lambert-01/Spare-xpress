Uploads are untrusted user data. The web server MUST NOT execute any script here.

Apache: uploads/.htaccess disables the PHP engine and denies script extensions.

Nginx (add inside the server block): deny script execution under /uploads/:
    location ^~ /uploads/ {
        location ~* \.(php|php3|php4|php5|php7|phtml|phar|cgi|pl|py|asp|aspx|jsp|sh)$ {
            deny all;
        }
    }
