<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case CreateProject = 'CreateProject';
    case ApproveProject = 'ApproveProject';
    case ClaimProject = 'ClaimProject';
    case ViewProject = 'ViewProject';
    case TrackProject = 'TrackProject';
    case Manage  = 'Manage';
    case Delete = 'Delete';
}