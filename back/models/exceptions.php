<?php

namespace Exceptions;

class BeloteException extends \Exception{}
class RepositoryRowsNotFound extends BeloteException{}
class CutOutOfRange extends BeloteException{}
class PlayerHasAlreadyPlayedCard extends BeloteException{}
class IllegalCard extends BeloteException{}
class TurnIsIncomplete extends BeloteException{}
class TurnNumberOutofBound extends BeloteException{}
class GameIsFinished extends BeloteException{}