find /etc/cron.d/ -type f | while read CRONTAB_FILE; do
        data="$(cat "$CRONTAB_FILE")"
        echo "$(export | cut -d ' ' -f 3- | grep '=')" > "$CRONTAB_FILE"
        echo "" >> "$CRONTAB_FILE"
        echo "$data" >> "$CRONTAB_FILE"
    done
