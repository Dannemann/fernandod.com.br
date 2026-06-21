# HostGator (on cPanel):

Tip: Most plans give one free domain. Redeem it from the HostGator backend after buying the plan. 

Create the database.  
Create the database user.  
Link the database with the user, granting its permissions.  
The database name, user, and password will be used for the PHP connection.  
Import `fernandod.sql`.  

Create the email account `notificacao@fernandod.com.br`.  
In Titan webmail inbox, enable external app access: "gear icon → `Ative o Titan nos outros aplicativos` → finish activation".  
This email address and its password will be used on PHP.

Create an FTP account.  
While creating the user, make sure its directory is something like "/home2/jeanda14/" and not "/home2/jeanda14/fernandod.com/deployment" (delete the contents of the "Diretório" field).

Production:  
https://fernandod.com.br

# Project configuration:

### Copy the essential configuration files:

`mail_config.example.php` or `mail_config.mailpit.example.php`  

`connector_data.example.php` or `connector_data.local.example.php`

`recaptcha_config.example.php`  
`FKD_RECAPTCHA_ENTERPRISE_API_KEY` can be created at `Google Cloud Console` → `APIs & Services` → `Credentials`.  
reCAPTCHA key creation can be found at `Google Cloud Console` → `Security` → `Fraud Defense`.

# Local:

Project infra:  
`docker compose -f infra/docker-compose.yml up -d`

Run the project:  
`php -S 127.0.0.1:8000`

Local:  
http://127.0.0.1:8000

Mailpit:  
http://localhost:8025

### Configure PHPStorm FTP Deployment (at the time I wrote this, only FTP was available; don't try FTPS).
`Tools` → `Deployment` → `Browse Remote Host`.  
`Root path` should be the deployment directory.  
On the `Mappings` tab, `Deployment path` and `Web path` should be `/`.  
Adjust `Excluded Paths` according to the exclusions in `compress-for-deploy.command`.  
