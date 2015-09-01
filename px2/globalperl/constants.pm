package constants;

use constant O_NONE                 => -1;
use constant O_ALL                  => -2;
use constant O_XELLIUM              =>  1;
use constant O_VITRA                =>  2;
use constant O_ENTRIUM              =>  3;
use constant O_AUGON                =>  4;
use constant O_MARIUM               =>  5;
use constant O_HALIGON              =>  6;

use constant B_HEADQUARTERS 	    =>  1;
use constant B_MINE 				=>  3;
use constant B_ORSERVATORY 			=>  6;
use constant B_VESSELSTATION 		=>  9;
use constant B_SPACEDOCK 	        => 11;
use constant B_EXPLORATIONSTATION 	=> 13;

use constant V_ADVANCED_EXPLORE     =>  2;

use constant I_DROIDS 				   =>  1;
use constant I_ORBIT_MINE              =>  2;
use constant I_LASER_CANNON            =>  3;
use constant I_PHOTON_LAUNCHER         =>  4;
use constant I_MINE_SWEEPER            => 14;
use constant I_STELLAR_LAUNCHER        => 15;
use constant I_WORMHOLE_STABILIZER     => 16;



use constant CAN_EXPLORE_SURFACE	   => 3;  # We have a exploration station
use constant CAN_EXPLORE_PLANETS	   => 2;  # We have a orbituarium
use constant CAN_EXPLORE_PENDING       => 1;  # This flag doesn't explore anything, but is used to notify that we
                                              # are currently in the process of making a orbituarium which can
                                              # explore.
use constant CAN_EXPLORE_NOTHING       => 0;  # Surface doesn't have a orbituarium nor is in the process of building one.


use constant PLANET_STATE_UNINHABITATED   =>   1;
use constant PLANET_STATE_NORMAL          =>   2;
use constant PLANET_STATE_UNDER_ATTACK    =>   3;
use constant PLANET_STATE_MEDICAL_ALERT   =>   4;
use constant PLANET_STATE_OVERPOPULATED   =>   5;
use constant PLANET_STATE_ALIEN_TAKEOVER  =>   6;
use constant PLANET_STATE_WORMHOLE        => 102;
use constant PLANET_STATE_BLACKHOLE       => 103;
use constant PLANET_STATE_NEBULA          => 104;
use constant PLANET_STATE_STARBASE        => 105;

use constant CAN_MINE                  => 2;  # We have a mining station
use constant CAN_MINE_PENDING          => 1;  # Currently in the process of building a mining station
use constant CAN_MINE_NOTHING          => 0;  # Cannot mine the planet because we don't have a mining station

use constant PLANET_STATE_UNDISCOVERED => 1;
use constant PLANET_STATE_DISCOVERED   => 0;

use constant UID_NOBODY => 0;

# PX_EF stuff
use constant ITEM_GENERIC      => 0;
use constant BUILDING_GENERIC  => 0;
use constant VESSEL_GENERIC    => 0;


# Messages
use constant MESSAGE_PRIO_LOW  => 1;
use constant MESSAGE_PRIO_HIGH => 2;

use constant MSG_TYPE_GLOBAL           => 'G';
use constant MSG_TYPE_USER             => 'U';
use constant MSG_TYPE_EXPLORATION      => 'E';
use constant MSG_TYPE_INVENTION        => 'I';
use constant MSG_TYPE_PLANET           => 'P';
use constant MSG_TYPE_VESSEL           => 'V';

use constant QUEUE_BUILD     => 'B';
use constant QUEUE_INVENTION => 'I';
use constant QUEUE_VESSEL    => 'V';
use constant QUEUE_FLIGHT    => 'F';
use constant QUEUE_UPGRADE   => 'U';

use constant ITEM_TYPE_WEAPON => 'W';
use constant ITEM_TYPE_VESSEL => 'V';
use constant ITEM_TYPE_PLANET => 'P';

use constant VESSEL_TYPE_EXPLORE => 'E';
use constant VESSEL_TYPE_TRADE   => 'T';
use constant VESSEL_TYPE_BATTLE  => 'B';

# Used for queries
use constant QUERY_KEEP   => 0;
use constant QUERY_NOKEEP => 1;

# Relations
use constant RELATION_FRIEND  => 1;
use constant RELATION_NEUTRAL => 2;
use constant RELATION_ENEMY   => 3;

use constant TICK_ITEM_ON_PLANET => 1;
use constant TICK_ITEM_ON_VESSEL => 2;

use constant ALLIANCE_FREE_ENTRY    => 0;
use constant ALLIANCE_OWNER_CONFIRM => 1;


# ----------------------------------------------------------------------------------------------------
# Return OK status to calling program
return 1;

#
# vim: ts=4 syntax=perl nowrap
#
