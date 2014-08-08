<?php

namespace Ws\EventsBundle\Event;

final class WsEvents {

	const SERIE_CREATE = 'ws.serie.new';

	const SERIE_DELETE = 'ws.serie.delete';

	const EVENT_CANCEL = 'ws.event.cancel';

	const EVENT_CHANGE = 'ws.event.cancel';

	const EVENT_CONFIRM = 'ws.event.confirm';

	const EVENT_DELETE = 'ws.event.delete';

	const EVENT_VIEW = 'ws.event.view';

	const PARTICIPANT_ADD = 'ws.participant.add';

	const PARTICIPANT_CANCEL = 'ws.participant.cancel';

	const CALENDAR_VIEW = 'ws.calendar.view';

	const CALENDAR_AJAX = 'ws.calendar.ajax';

	const CALENDAR_RESET = 'ws.calendar.reset';
}