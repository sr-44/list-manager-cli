<?php

namespace App;

enum ActionsType: string
{
    case ADD = 'add';
    case CHANGE = 'change';
    case REMOVE = 'remove';
    case CALCULATE = 'calculate';
}
