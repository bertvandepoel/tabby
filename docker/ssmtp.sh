#!/bin/bash
echo "root=postmaster" > /etc/ssmtp/ssmtp.conf
echo "mailhub=$TABBY_SMTP_SERVER" > /etc/ssmtp/ssmtp.conf
if [[ -n "$TABBY_SMTP_USER" ]]; then
    echo "authuser=$TABBY_SMTP_USER" >> /etc/ssmtp/ssmtp.conf
    echo "authpass=$TABBY_SMTP_PASSWORD" >> /etc/ssmtp/ssmtp.conf
fi
