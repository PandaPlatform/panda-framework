<?php

/*
 * This file is part of the Panda framework Log component.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Panda\Log;

use Monolog\Logger as MonoLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;

/**
 * Panda Logger interface
 *
 */
class Logger extends MonoLogger implements LoggerInterface
{

}
?>