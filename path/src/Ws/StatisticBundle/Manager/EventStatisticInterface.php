<?php

namespace Ws\StatisticBundle\Manager;

interface EventStatisticInterface
{
    /**
     * Return an array of StatLogic
     */
    public function getStatLogics();

    /**
     * Return the name of the event
     *
     * will be converted in Statistic field by the parameters in the "context".yml
     */
    public function getName();
}
