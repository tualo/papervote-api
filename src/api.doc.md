# 🧩 Werbemittelmanager-Anbindung API Dokumentation

## 📘 Übersicht

Diese API gehört zur **Werbemittelmanager-Anbindung** unter der Basis-URL:

```
https://muenchen.wahl.software/wm/
```

Zweck der API ist es, angemeldete Benutzer anhand eines eindeutigen **Keys** zu identifizieren und deren Informationen bereitzustellen.

Alle Anfragen müssen zusätzlich mit einem gültigen **Authorization Header** versehen werden.  
Den zugehörigen **Token** erhält man gesondert vom Betreiber des Systems.

---

## 🔐 Authentifizierung

Jede Anfrage erfordert:

```
Authorization: <token>
```

- `<token>` ist der vom System vergebene API-Zugriffstoken.  
- Zusätzlich muss in der URL der **Key** übergeben werden:
  ```
  ?api_key=<api_key>
  ```

Beispiel für eine vollständige Anfrage:
```
GET https://muenchen.wahl.software/wm/papervote-api/ping?api_key=abc123
```

---

## 📡 Endpunkte

### 1. **Ping-Route**

**URL:**
```
GET https://muenchen.wahl.software/wm/papervote-api/ping?api_key=<api_key>
```

**Beschreibung:**
Überprüft, ob der Benutzer korrekt angemeldet ist und der übergebene Key gültig ist.

**Rückgabe (Beispiel):**
```json
{
    "msg": "",
    "success": true,
    "errors": [],
    "warnings": []
}
```

**cURL-Beispiel:**
```bash
curl -X GET "https://muenchen.wahl.software/wm/papervote-api/ping?api_key=abc123"      -H "Authorization: Bearer YOUR_AUTH_TOKEN"      -H "Accept: application/json"
```

Wenn `success: true` zurückgegeben wird, ist der Benutzer gültig angemeldet.

---

### 2. **Info-Route**

**URL:**
```
GET https://muenchen.wahl.software/wm/papervote-api/info?api_key=<api_key>
```

**Beschreibung:**
Liefert detaillierte Informationen über den angemeldeten Benutzer und seine im System gespeicherten Daten.
Auch beim Abrufen der Bild muss der Authorization Header mitgeliefert werden.

**Rückgabe (Beispiel):**
```json
{
    "msg": "",
    "success": true,
    "errors": [],
    "warnings": [],
    "info": {
        "user": "thomas.hoffmann@tualo.de",
        "email": "thomas.hoffmann@tualo.de",
        "vorname": "Berthold",
        "nachname": "Rickert",
        "statement1": "Mustertext",
        "statement2": "Mustertex",
        "statement3": "Mustertext",
        "original_portrait_url": "https://muenchen.wahl.software/wm/papervote-api/portrait/<bildid>",
        "cropped_portrait_url": "https://muenchen.wahl.software/wm/papervote-api/portrait/<bildid2>"
    }
}
```

**cURL-Beispiel:**
```bash
curl -X GET "https://muenchen.wahl.software/wm/papervote-api/info?api_key=abc123"      -H "Authorization: Bearer YOUR_AUTH_TOKEN"      -H "Accept: application/json"
```

**Erklärung der Felder:**

| Feld | Beschreibung |
|------|---------------|
| `user`, `email` | Benutzeridentität |
| `vorname`, `nachname` | Name des Benutzers |
| `statement1-3` | Benutzerdefinierte Textfelder (z. B. Slogans, Botschaften etc.) |
| `original_portrait_url` | URL zum Originalportrait des Benutzers |
| `cropped_portrait_url` | URL zur zugeschnittenen Variante des Portraits |

---

## ⚙️ Zusammenfassung

| Endpunkt | Methode | Zweck |
|-----------|----------|-------|
| `/papervote-api/ping?api_key=<api_key>` | GET | Prüft, ob ein Benutzer angemeldet ist |
| `/papervote-api/info?api_key=<api_key>` | GET | Liefert Benutzerinformationen und Bild-URLs |

Beide Endpunkte benötigen:
- einen gültigen `Authorization` Header  
- den Parameter `api_key` in der URL

---

## 💡 Hinweise

- Die API gibt strukturierte JSON-Antworten zurück.  
- Fehler werden im Feld `errors` gesammelt.  
- Warnungen (z. B. veraltete Daten) erscheinen im Feld `warnings`.  
- Der Wert `success` ist **true**, wenn die Anfrage erfolgreich war.  

---

