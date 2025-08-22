# IPSHomeAssistantPergola

**Pergola Steuerung**  
Ein Zusatzmodul für IP-Symcon zur Steuerung von Home Assistant Covern (Vorhänge, Lamellen, Ein-/Ausfahren) sowie LED-Licht.  

Das Modul baut auf dem [IPSHomeAssistantConnector](https://github.com/AndreasWalder/IPSHomeAssistantConnector) auf und ermöglicht komfortables Testen und Steuern über Variablen und WebFront.

---

## ✨ Features
- **LED Steuerung (light.*)**  
  - An / Aus  
  - Dimmen (0–100 %)  

- **Cover Steuerung (cover.*)**  
  - Sammelvariable (Stopp / Auf / Zu)  
  - Tilt-Variable (0–100 %)  
  - Je Cover: Vorhang vorne, hinten, links, rechts, Lamellen, Ein-/Ausfahren  

- **Test-Buttons im Konfigurationsformular**  
  - LED: ON / OFF / DIM 50 %  
  - Cover: OPEN / CLOSE / STOP / TILT 50 %  

---

## 🛠️ Installation
1. Repository in deinen `IP-Symcon/modules` Ordner klonen:
   ```bash
   git clone https://github.com/AndreasWalder/IPSHomeAssistantPergola
   ```
2. Symcon Dienst neu starten.
3. Neue Instanz anlegen:  
   Objektbaum → Instanz hinzufügen → „Pergola“
4. Connector-Instanz (IPSHomeAssistantConnector) auswählen und Entities eintragen.  

---

## ⚙️ Konfiguration
Im Formular:  
- Auswahl der **Connector-Instanz**  
- Eingabe der Home Assistant Entities für LED + Cover  

Im Objektbaum werden Variablen erzeugt:  
- **Pergola LED (Bool)** – AN/AUS  
- **Pergola Dimmer % (Int)** – Helligkeit  
- **Cover [Name] (Int)** – Stopp/Auf/Zu  
- **Cover [Name] Tilt % (Int)** – Neigungswinkel  

---

## 💡 Beispiel
- `light.led_dach_led_dach` → Pergola LED  
- `cover.vorhang_vorne` → Cover Vorhang vorne  
- `cover.lamellen_schwenken` → Cover Lamellen  

---

## 🧑‍💻 Autor & Lizenz
- Erstellt von **Andreas Walder**  
- MIT-Lizenz (siehe LICENSE)

---