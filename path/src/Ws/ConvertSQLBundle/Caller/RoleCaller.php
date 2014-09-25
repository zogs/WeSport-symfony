<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class RoleCaller extends AbstractCaller
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
}