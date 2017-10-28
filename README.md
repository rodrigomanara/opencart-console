**Opencart Extension Export**

this cli will help you find and compile all files that were create to build an extension for opencart

CLI still need to add the validations but for now it does the job to find the files also export to an unique folder

> To install it you will need to go to composer

 - add the script lines into your opencart / composer.json  
 - run the command to install it

> composer install
    
<pre><code>
{
    "name": "opencart/opencart",
	   .....
    },
    "require": {
        "rmanara/console": "@dev"
    },
    "scripts": {
        "post-install-cmd": [
            "Rmanara\\Lib\\Installer::Init"
        ],
        "post-update-cmd": [
            "Rmanara\\Lib\\Installer::Init"
        ]
   }
}
</code> </pre> 

After the installation you will a notification saying:
file console ready 

now you can run the cli to export the extension files that you were working on

> php console app:export {filename}

> php console app:build 

> php console app:data {dump|optmise}

it will build the folder **"/temp_extension"** and put all file that has that name in it