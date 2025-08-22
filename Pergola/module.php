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

    public function RequestAction($Ident, $Value)
    {
        $this->SendDebug('RequestAction', $Ident . ' => ' . $Value, 0);
        switch ($Ident) {
            case self::VID_LIGHT_POWER:
                $this->setLightPower((bool)$Value);
                break;
            case self::VID_LIGHT_DIM:
                $this->setLightDim((int)$Value);
                break;

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

    // --- Testfunktionen
    public function TestLightOn()        { $this->setLightPower(true); }
    public function TestLightOff()       { $this->setLightPower(false); }
    public function TestLightDim50()     { $this->setLightDim(50); }
    public function TestCoverOpenVorne() { $this->driveCoverCmd($this->ReadPropertyString('CoverVorne'), 1); }

    // --- Lichtsteuerung
    private function setLightPower(bool $on): void
    {
        $entity = $this->ReadPropertyString('LightPergola');
        $iid = $this->getConnector();
        if ($entity === '' || $iid === 0) return;

        $this->SendDebug('setLightPower', ($on ? 'ON' : 'OFF'), 0);
        if ($on) {
            $result = HAC_TurnOn($iid, 100, null, $entity);
        } else {
            $result = HAC_TurnOff($iid, $entity);
        }
        $this->SendDebug('LightResponse', $result, 0);
        $this->SetValue(self::VID_LIGHT_POWER, $on);
        if (!$on) $this->SetValue(self::VID_LIGHT_DIM, 0);
    }

    private function setLightDim(int $pct): void
    {
        $pct = max(0, min(100, $pct));
        $entity = $this->ReadPropertyString('LightPergola');
        $iid = $this->getConnector();
        if ($entity === '' || $iid === 0) return;

        $this->SendDebug('setLightDim', $pct, 0);
        $result = HAC_SetPercent($iid, $entity, $pct);
        $this->SendDebug('DimResponse', $result, 0);
        $this->SetValue(self::VID_LIGHT_DIM, $pct);
        $this->SetValue(self::VID_LIGHT_POWER, $pct > 0);
    }

    private function driveCoverCmd(string $entity, int $cmd): void
    {
        if ($entity === '') return;
        $iid = $this->getConnector();
        if ($iid === 0) return;

        $this->SendDebug('driveCoverCmd', $entity . ' -> ' . $cmd, 0);
        switch ($cmd) {
            case 1: $r = HAC_CallService($iid, 'cover', 'open_cover',  ['entity_id' => $entity]); break;
            case 2: $r = HAC_CallService($iid, 'cover', 'close_cover', ['entity_id' => $entity]); break;
            default:$r = HAC_CallService($iid, 'cover', 'stop_cover',  ['entity_id' => $entity]); break;
        }
        $this->SendDebug('CoverResponse', $r, 0);
    }

    private function setTilt(string $entity, int $pct): void
    {
        $pct = max(0, min(100, $pct));
        if ($entity === '') return;
        $iid = $this->getConnector();
        if ($iid === 0) return;

        $this->SendDebug('setTilt', $entity . ' = ' . $pct, 0);
        $r = HAC_CallService($iid, 'cover', 'set_cover_tilt_position', [
            'entity_id'     => $entity,
            'tilt_position' => $pct
        ]);
        $this->SendDebug('TiltResponse', $r, 0);
    }

    private function createVariables(): void
    {
        $this->RegisterVariableBoolean(self::VID_LIGHT_POWER, 'Pergola LED', '~Switch', 10);
        $this->EnableAction(self::VID_LIGHT_POWER);

        $this->RegisterVariableInteger(self::VID_LIGHT_DIM, 'Pergola Dimmer %', '~Intensity.100', 20);
        $this->EnableAction(self::VID_LIGHT_DIM);

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
            IPS_CreateVariableProfile('HAP.CoverCmd', 1);
            IPS_SetVariableProfileAssociation('HAP.CoverCmd', 0, 'Stopp', '', -1);
            IPS_SetVariableProfileAssociation('HAP.CoverCmd', 1, 'Auf', '', -1);
            IPS_SetVariableProfileAssociation('HAP.CoverCmd', 2, 'Zu', '', -1);
        }
    }

    private function getConnector(): int
    {
        return (int)$this->ReadPropertyInteger('ConnectorInstance');
    }
}
