<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class UsersCaller extends AbstractCaller
{
	
	public function setRoles()
	{
		if($this->entry['account'] == 'public'){

			return array('ROLE_USER');
		}

		if($this->entry['account'] == 'asso'){

			return array('ROLE_ASSO');
		}

		if($this->entry['account'] == 'bizness'){

			return array('ROLE_BIZNESS');
		}

		return array('ROLE_USER');
	}

	public function setGender()
	{
		if($this->entry['sexe'] == 'h') return 'm';

		if($this->entry['sexe'] == 'f') return 'f';

		return null;
	}

	public function setSettings()
	{
		$db = $this->container->get('doctrine.dbal.oldwesport_connection');

		$stmt = $db->prepare('SELECT * FROM users_settings_mailing WHERE user_id='.$this->entry['user_id'].' LIMIT 1');
		$stmt->execute();
		$old = $stmt->fetch();

		$settings = new \My\UserBundle\Entity\Settings();

		if(empty($old)) return $settings;

		$mailer_settings = new \Ws\MailerBundle\Entity\Settings();
		$mailer_settings->setEventConfirmed($old['eventConfirmed']);
		$mailer_settings->setEventCanceled($old['eventCanceled']);
		$mailer_settings->setEventChanged($old['eventChanged']);
		$mailer_settings->setEventOpinion($old['eventOpinion']);
		$mailer_settings->setEventUserQuestion($old['eventUserQuestion']);
		$mailer_settings->setEventOrganizerAnswer($old['eventOrgaReply']);
		$mailer_settings->setEventAddParticipation($old['eventNewParticipant']);
		$mailer_settings->setEventCancelParticipation(1);

		$settings->setWsMailerSettings($mailer_settings);

		return $settings;
	}
}