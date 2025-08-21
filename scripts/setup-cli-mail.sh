#!/bin/sh

echo "This scripts is not meant to be launched"
exit 1

# Installation
sudo apt install msmtp msmtp-mta


# Configuration
touch ~/.msmtprc
chmod 600 ~/.msmtprc

cat >> ~/.msmtprc<< EOF
defaults
auth           on
tls            on
tls_trust_file /etc/ssl/certs/ca-certificates.crt
logfile        ~/.msmtp.log

account        my-account-any-name
host           smtp.gmail.com
port           587
from           mail@gmail.com
user           mail@gmail.com
password       must_stay_secret

account default : my-account-any-name
EOF


# Usage
echo -e "Subject: Deployment launched !\nFrom service any name<source@gmail.com>\n\nApp is being redeployed" | msmtp target@gmail.com

