<?php
declare(strict_types=1);

class Pergola extends IPSModule
{
    // Sammelvariable-Idents
    private const VID_LIGHT_POWER      = 'LightPergolaPower';
    private const VID_LIGHT_DIM        = 'LightPergolaDim';

    private const VID_COVER_VORNE      = 'CoverVorneCtrl';
    private const VID_TILT_VORNE       = 'CoverVorneTilt';

    private const VID_COVER_HINTEN     = 'CoverHintenCtrl';
    private const VID_TILT_HINTEN      = 'CoverHintenTilt';

    private const VID_COVER_LINKS      = 'CoverLinksCtrl';
    private const VID_TILT_LINKS       = 'CoverLinksTilt';

    private const VID_COVER_RECHTS     = 'CoverRechtsCtrl';
    private const VID_TILT_RECHTS      = 'CoverRechtsTilt';

    private const VID_COVER_LAMELLEN   = 'CoverLamellenCtrl';
    private const VID_TILT_LAMELLEN    = 'CoverLamellenTilt';

    private const VID_COVER_EINAUS     = 'CoverEinAusCtrl';
    private const VID_TILT_EINAUS      = 'CoverEinAusTilt';

    public function Create()
    {
        parent::Create();
        // Connector + Entities
        $this->RegisterPropertyInteger('ConnectorInstance', 0);

        $this->RegisterPropertyString('LightPergola', '');
        $this->RegisterPropertyString('LightHeizung', '');

        $this->RegisterPropertyString('CoverVorne', '');
        $this->RegisterPropertyString('CoverHinten', '');
        $this->RegisterPropertyString('CoverLinks', '');
        $this->RegisterPropertyString('CoverRechts', '');
        $this->RegisterPropertyString('CoverLamellen', '');
        $this->RegisterPropertyString('CoverEinAus', '');

        $this->ensureProfiles();
        $this->createVariables();
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
    }

    // -------- WebFront Actions --------
    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            // Light
            case self::VID_LIGHT_POWER:
                $this->setLightPower((bool)$Value);
                break;
            case self::VID_LIGHT_DIM:
                $this->setLightDim((int)$Value);
                break;

            // Cover Sammelvariablen
            case self::VID_COVER_VORNE:
                $this->driveCoverCmd($this->ReadPropertyString('CoverVorne'), (int)$Value);
                $this->SetValue(self::VID_COVER_VORNE, 0);
                break;
            case self::VID_TILT_VORNE:
                $this->setTilt($this->ReadPropertyString('CoverVorne'), (int)$Value);
                break;

            case self::VID_COVER_HINTEN:
                $this->driveCoverCmd($this->ReadPropertyString('CoverHinten'), (int)$Value);
                $this->SetValue(self::VID_COVER_HINTEN, 0);
                break;
            case self::VID_TILT_HINTEN:
                $this->setTilt($this->ReadPropertyString('CoverHinten'), (int)$Value);
                break;

            case self::VID_COVER_LINKS:
                $this->driveCoverCmd($this->ReadPropertyString('CoverLinks'), (int)$Value);
                $this->SetValue(self::VID_COVER_LINKS, 0);
                break;
            case self::VID_TILT_LINKS:
                $this->setTilt($this->ReadPropertyString('CoverLinks'), (int)$Value);
                break;

            case self::VID_COVER_RECHTS:
                $this->driveCoverCmd($this->ReadPropertyString('CoverRechts'), (int)$Value);
                $this->SetValue(self::VID_COVER_RECHTS, 0);
                break;
            case self::VID_TILT_RECHTS:
                $this->setTilt($this->ReadPropertyString('CoverRechts'), (int)$Value);
                break;

            case self::VID_COVER_LAMELLEN:
                $this->driveCoverCmd($this->ReadPropertyString('CoverLamellen'), (int)$Value);
                $this->SetValue(self::VID_COVER_LAMELLEN, 0);
                break;
            case self::VID_TILT_LAMELLEN:
                $this->setTilt($this->ReadPropertyString('CoverLamellen'), (int)$Value);
                break;

            case self::VID_COVER_EINAUS:
                $this->driveCoverCmd($this->ReadPropertyString('CoverEinAus'), (int)$Value);
                $this->SetValue(self::VID_COVER_EINAUS, 0);
                break;
            case self::VID_TILT_EINAUS:
                $this->setTilt($this->ReadPropertyString('CoverEinAus'), (int)$Value);
                break;

            default:
                throw new Exception("Unknown ident: $Ident");
        }
    }

    // -------- Formular-Buttons (Tests) --------
    public function TestLightOn()   { $this->setLightPower(true); }
    public function TestLightOff()  { $this->setLightPower(false); }
    public function TestLightDim50(){ $this->setLightDim(50); }

    public function TestCoverOpenVorne()   { $this->driveCoverCmd($this->ReadPropertyString('CoverVorne'), 1); }
    public function TestCoverCloseVorne()  { $this->driveCoverCmd($this->ReadPropertyString('CoverVorne'), 2); }
    public function TestCoverStopVorne()   { $this->driveCoverCmd($this->ReadPropertyString('CoverVorne'), 0); }
    public function TestTiltVorne50()      { $this->setTilt($this->ReadPropertyString('CoverVorne'), 50); }

    public function TestCoverOpenHinten()  { $this->driveCoverCmd($this->ReadPropertyString('CoverHinten'), 1); }
    public function TestCoverCloseHinten() { $this->driveCoverCmd($this->ReadPropertyString('CoverHinten'), 2); }
    public function TestCoverStopHinten()  { $this->driveCoverCmd($this->ReadPropertyString('CoverHinten'), 0); }
    public function TestTiltHinten50()     { $this->setTilt($this->ReadPropertyString('CoverHinten'), 50); }

    public function TestCoverOpenLinks()   { $this->driveCoverCmd($this->ReadPropertyString('CoverLinks'), 1); }
    public function TestCoverCloseLinks()  { $this->driveCoverCmd($this->ReadPropertyString('CoverLinks'), 2); }
    public function TestCoverStopLinks()   { $this->driveCoverCmd($this->ReadPropertyString('CoverLinks'), 0); }
    public function TestTiltLinks50()      { $this->setTilt($this->ReadPropertyString('CoverLinks'), 50); }

    public function TestCoverOpenRechts()  { $this->driveCoverCmd($this->ReadPropertyString('CoverRechts'), 1); }
    public function TestCoverCloseRechts() { $this->driveCoverCmd($this->ReadPropertyString('CoverRechts'), 2); }
    public function TestCoverStopRechts()  { $this->driveCoverCmd($this->ReadPropertyString('CoverRechts'), 0); }
    public function TestTiltRechts50()     { $this->setTilt($this->ReadPropertyString('CoverRechts'), 50); }

    public function TestCoverOpenLamellen(){ $this->driveCoverCmd($this->ReadPropertyString('CoverLamellen'), 1); }
    public function TestCoverCloseLamellen(){ $this->driveCoverCmd($this->ReadPropertyString('CoverLamellen'), 2); }
    public function TestCoverStopLamellen(){ $this->driveCoverCmd($this->ReadPropertyString('CoverLamellen'), 0); }
    public function TestTiltLamellen50()   { $this->setTilt($this->ReadPropertyString('CoverLamellen'), 50); }

    public function TestCoverOpenEinAus()  { $this->driveCoverCmd($this->ReadPropertyString('CoverEinAus'), 1); }
    public function TestCoverCloseEinAus() { $this->driveCoverCmd($this->ReadPropertyString('CoverEinAus'), 2); }
    public function TestCoverStopEinAus()  { $this->driveCoverCmd($this->ReadPropertyString('CoverEinAus'), 0); }
    public function TestTiltEinAus50()     { $this->setTilt($this->ReadPropertyString('CoverEinAus'), 50); }

    // -------- Logik --------
    private function setLightPower(bool $on): void
    {
        $entity = $this->ReadPropertyString('LightPergola');
        $iid = $this->getConnector();
        $this->SendDebug(__FUNCTION__, "Entity: $entity / Power: " . ($on ? 'ON' : 'OFF'), 0);
    
        if ($entity === '' || $iid === 0) {
            $this->SendDebug(__FUNCTION__, 'Abbruch: Entity oder Connector leer', 0);
            return;
        }
    
        if ($on) {
            $result = HAC_TurnOn($iid, 100, null, $entity);
        } else {
            $result = HAC_TurnOff($iid, $entity);
        }
    
        $this->SetValue(self::VID_LIGHT_POWER, $on);
        if (!$on) {
            $this->SetValue(self::VID_LIGHT_DIM, 0);
        }
    
        $this->SendDebug(__FUNCTION__, 'Antwort: ' . json_encode($result), 0);
    }

    
    private function setLightDim(int $pct): void
    {
        $pct = max(0, min(100, $pct));
        $entity = $this->ReadPropertyString('LightPergola');
        $iid = $this->getConnector();
        if ($entity === '' || $iid === 0) return;

        HAC_SetPercent($iid, $entity, $pct);
        $this->SetValue(self::VID_LIGHT_DIM, $pct);
        $this->SetValue(self::VID_LIGHT_POWER, $pct > 0);
    }

    private function driveCoverCmd(string $entity, int $cmd): void
    {
        $iid = $this->getConnector();
        $this->SendDebug(__FUNCTION__, "Entity: $entity / Command: $cmd", 0);
    
        if ($entity === '' || $iid === 0) {
            $this->SendDebug(__FUNCTION__, 'Abbruch: Entity oder Connector leer', 0);
            return;
        }
    
        switch ($cmd) {
            case 1:
                $result = HAC_CallService($iid, 'cover', 'open_cover', ['entity_id' => $entity]);
                break;
            case 2:
                $result = HAC_CallService($iid, 'cover', 'close_cover', ['entity_id' => $entity]);
                break;
            default:
                $result = HAC_CallService($iid, 'cover', 'stop_cover', ['entity_id' => $entity]);
                break;
        }
    
        $this->SendDebug(__FUNCTION__, 'Antwort: ' . json_encode($result), 0);
    }


    private function setTilt(string $entity, int $pct): void
    {
        $iid = $this->getConnector();
        $this->SendDebug(__FUNCTION__, "Entity: $entity / Tilt: $pct", 0);
    
        if ($entity === '' || $iid === 0) {
            $this->SendDebug(__FUNCTION__, 'Abbruch: Entity oder Connector leer', 0);
            return;
        }
    
        $result = HAC_CallService($iid, 'cover', 'set_cover_tilt_position', [
            'entity_id'     => $entity,
            'tilt_position' => $pct
        ]);
    
        $this->SendDebug(__FUNCTION__, 'Antwort: ' . json_encode($result), 0);
    }


    private function createVariables(): void
    {
        // Light
        $this->RegisterVariableBoolean(self::VID_LIGHT_POWER, 'Pergola LED', '~Switch', 10);
        $this->EnableAction(self::VID_LIGHT_POWER);

        $this->RegisterVariableInteger(self::VID_LIGHT_DIM, 'Pergola Dimmer %', '~Intensity.100', 20);
        $this->EnableAction(self::VID_LIGHT_DIM);

        // Cover Sammelvariablen (0 Stop, 1 Auf, 2 Zu) und Tilt 0–100
        $this->registerCoverWithTilt(self::VID_COVER_VORNE,  self::VID_TILT_VORNE,  'Vorhang vorne',  30);
        $this->registerCoverWithTilt(self::VID_COVER_HINTEN, self::VID_TILT_HINTEN, 'Vorhang hinten', 40);
        $this->registerCoverWithTilt(self::VID_COVER_LINKS,  self::VID_TILT_LINKS,  'Vorhang links',  50);
        $this->registerCoverWithTilt(self::VID_COVER_RECHTS, self::VID_TILT_RECHTS, 'Vorhang rechts', 60);
        $this->registerCoverWithTilt(self::VID_COVER_LAMELLEN,self::VID_TILT_LAMELLEN,'Lamellen',      70);
        $this->registerCoverWithTilt(self::VID_COVER_EINAUS, self::VID_TILT_EINAUS, 'Einfahren/Ausfahren', 80);
    }

    private function registerCoverWithTilt(string $ctrlIdent, string $tiltIdent, string $caption, int $pos): void
    {
        $this->RegisterVariableInteger($ctrlIdent, $caption, 'HAP.CoverCmd', $pos);
        $this->EnableAction($ctrlIdent);
        $this->SetValue($ctrlIdent, 0);

        $this->RegisterVariableInteger($tiltIdent, $caption . ' Tilt %', '~Intensity.100', $pos + 1);
        $this->EnableAction($tiltIdent);
    }

    private function ensureProfiles(): void
    {
        if (!IPS_VariableProfileExists('HAP.CoverCmd')) {
            IPS_CreateVariableProfile('HAP.CoverCmd', 1 /* Integer */);
            IPS_SetVariableProfileAssociation('HAP.CoverCmd', 0, 'Stopp', '', -1);
            IPS_SetVariableProfileAssociation('HAP.CoverCmd', 1, 'Auf',   '', -1);
            IPS_SetVariableProfileAssociation('HAP.CoverCmd', 2, 'Zu',    '', -1);
        }
    }

    private function getConnector(): int
    {
        return (int)$this->ReadPropertyInteger('ConnectorInstance');
    }
}
