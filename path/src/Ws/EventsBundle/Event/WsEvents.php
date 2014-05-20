<?php

namespace Ws\EventsBundle\Event;

final class WsEvents {

	const CREATE_EVENTS = 'ws.events.new';

	const CANCEL_EVENT = 'ws.events.cancel';

	const CONFIRM_EVENT = 'ws.events.confirm';

	const DELETE_EVENT = 'ws.events.delete';

	const VIEW_EVENT = 'ws.events.view';

	const DELETE_SERIE = 'ws.serie.delete';

	const ADD_PARTICIPANT = 'ws.participant.add';

	const CANCEL_PARTICIPANT = 'ws.participant.cancel';
}