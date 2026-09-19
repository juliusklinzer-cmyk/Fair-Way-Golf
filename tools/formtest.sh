cd ~/projects/fairwaygolf
B=http://localhost:8092
N=$(curl -s $B/voranmeldung/ | grep -oE 'name="nonce" value="[a-f0-9]+"' | head -1 | grep -oE '[a-f0-9]{8,12}')
TS=$(( $(date +%s) - 10 ))
post() { curl -s -X POST "$B/wp-admin/admin-ajax.php" --data-urlencode action=fwg_form --data-urlencode "nonce=$N" --data-urlencode "ts=$TS" "$@"; echo; }
echo "nonce: $N"
echo "--- 1 voranmeldung gültig + updates ---"; post -d form=voranmeldung -d quelle=voranmeldung --data-urlencode "vorname=Test" --data-urlencode "nachname=Golfer" --data-urlencode "email=test@example.com" --data-urlencode "ort=München" --data-urlencode "wunschplaetze=GC Valley, GC Eichenried" -d kategorie=m -d updates=1 -d datenschutz=1
echo "--- 2 voranmeldung ungültig ---"; post -d form=voranmeldung --data-urlencode "vorname=" --data-urlencode "email=kaputt" --data-urlencode "wunschplaetze="
echo "--- 3 honeypot ---"; post -d form=voranmeldung --data-urlencode "website=spam" --data-urlencode "vorname=Bot" --data-urlencode "email=bot@example.com" --data-urlencode "wunschplaetze=x" -d datenschutz=1
echo "--- 4 golfanlage ---"; post -d form=golfanlage -d quelle=golfplaetze --data-urlencode "anlage=GC Testplatz" --data-urlencode "name=Maria Manager" --data-urlencode "email=club@example.com" --data-urlencode "telefon=+49 89 123" -d kontaktweg=telefon --data-urlencode "nachricht=Bitte<b>anrufen</b>
Zeile 2" -d datenschutz=1
echo "--- 5 unterstuetzen ---"; post -d form=unterstuetzen -d quelle=unterstuetzen --data-urlencode "name=Ingo Investor" --data-urlencode "email=invest@example.com" -d rolle=investor -d datenschutz=1
echo "--- 6 lieblingsplatz ---"; post -d form=lieblingsplatz -d quelle=start --data-urlencode "platz=GC Valley"
echo "--- 7 falsche nonce ---"; curl -s -o /dev/null -w "%{http_code}\n" -X POST "$B/wp-admin/admin-ajax.php" -d action=fwg_form -d form=lieblingsplatz -d nonce=abc -d platz=x
echo "--- mails in MailHog ---"
curl -s http://localhost:8027/api/v2/messages > /tmp/mh.json
python3 - <<'PY'
import json,re,quopri
d=json.load(open('/tmp/mh.json')); print("total:",d["total"])
tok=None
for m in d["items"]:
    h=m["Content"]["Headers"]; subj=h.get("Subject",["?"])[0]; to=h.get("To",["?"])[0]; frm=h.get("From",["?"])[0]
    print(" ", subj, "|", frm, "->", to)
    body=quopri.decodestring(m["Content"]["Body"].encode()).decode("utf-8","ignore")
    mm=re.search(r'fwg_bestaetigen=([a-z0-9]{32})',body)
    if mm: tok=mm.group(1)
open('/tmp/tok','w').write(tok or '')
print("doi token:",tok)
PY
TOK=$(cat /tmp/tok)
echo "--- 8 DOI bestätigen ---"; curl -s -o /dev/null -w "%{http_code} -> %{redirect_url}\n" "$B/voranmeldung/?fwg_bestaetigen=$TOK"
echo "--- 9 abmelden ---"; curl -s -o /dev/null -w "%{http_code} -> %{redirect_url}\n" "$B/voranmeldung/?fwg_abmelden=$TOK"
echo "--- 10 ungültiger token ---"; curl -s -o /dev/null -w "%{http_code} -> %{redirect_url}\n" "$B/voranmeldung/?fwg_bestaetigen=00000000000000000000000000000000"
echo "--- 11 Nicht-JS-Weg (admin-post) ---"; curl -s -o /dev/null -w "%{http_code} -> %{redirect_url}\n" -X POST "$B/wp-admin/admin-post.php" -H "Referer: $B/unterstuetzen/" --data-urlencode action=fwg_form --data-urlencode "nonce=$N" --data-urlencode "ts=$TS" -d form=unterstuetzen --data-urlencode "name=Nojs Nutzer" --data-urlencode "email=nojs@example.com" -d rolle=partner -d datenschutz=1
echo "--- Anmeldungen in WP ---"
./wp.sh post list --post_type=fwg_anmeldung --fields=ID,post_title,post_date --format=csv </dev/null
ID=$(./wp.sh post list --post_type=fwg_anmeldung --field=ID --posts_per_page=1 --orderby=ID --order=ASC </dev/null | tr -d '\r\n')
echo "meta of #$ID:"; ./wp.sh post meta list $ID --fields=meta_key,meta_value --format=csv </dev/null | grep -vE "token|client"
