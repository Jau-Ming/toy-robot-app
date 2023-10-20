<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

class ToyRobotController extends Controller
{

    protected $robot_pos_x = null;
    protected $robot_pos_y = null;
    protected $robot_facing = null;
    protected $x_axis_max = null;
    protected $y_axis_max = null;
    protected $facing_options = ['NORTH', 'SOUTH', 'EAST', 'WEST'];

    public function __construct($x_max, $y_max)
    {
        $this->x_axis_max = $x_max - 1;
        $this->y_axis_max = $y_max - 1;

        $this->robot_pos_x = null;
        $this->robot_pos_y = null;
    }

    /**
     * Read commands from a specific .txt file located at ~/storage/app/public/
     * @param string $filename
     */
    public function read_commands_file(string $filename)
    {
        $resul = null;
        File::lines(storage_path('app/public/' . $filename))->each(function ($line) use (&$resul) {
            $resul = $this->execute_command($line);
            // Stop loop if REPORT is executed
            if (str_contains($resul, 'Output')) return false;
        });

        return (str_contains($resul, 'Output')) ? $resul : 'Command REPORT not provided';
    }

    /**
     * Executes commands from a string
     * @param string $line can be from file or standard input
     */
    public function execute_command(string $line)
    {
        $line = strtoupper($line);
        if (str_contains($line, 'PLACE')) {
            $coordinates = explode(',', explode(' ', $line)[1]);
            if (is_numeric($coordinates[0]) && is_numeric($coordinates[1]) && in_array($coordinates[2], $this->facing_options)) {
                return $this->set_place($coordinates[0], $coordinates[1], $coordinates[2]);
            }
        }

        // If position is not initialised this commansd are ignored
        if (!is_null($this->robot_pos_x) && !is_null($this->robot_pos_y)) {
            if (str_contains($line, 'MOVE')) {
                return $this->move();
            }
            if (str_contains($line, 'LEFT')) {
                return $this->change_facing('LEFT');
            }
            if (str_contains($line, 'RIGHT')) {
                return $this->change_facing('RIGHT');
            }
        }

        if (str_contains($line, 'REPORT')) {
            return $this->report();
        }
    }

    /**
     * Sets position and orientation of the robot in de board
     * @param int $x x coordinate
     * @param int $y y coordinate
     * @param string $facing values 'NORTH', 'SOUTH', 'EAST', 'WEST'
     */
    public function set_place(int $x, int $y, string $facing)
    {

        // position initialise only if values are into the range of the max lengh
        if ($x > $this->x_axis_max || $y > $this->x_axis_max) {
            return false;
        } else {
            $this->robot_pos_x = $x;
            $this->robot_pos_y = $y;
            $this->robot_facing = $facing;
            return true;
        }
    }

    /**
     * Moves the robot along 1 unit the board accordint the orientation only if the destination possition is viable
     */
    public function move()
    {
        switch ($this->robot_facing) {
            case 'NORTH':
                if ($this->robot_pos_y + 1 <= $this->y_axis_max) {
                    $this->robot_pos_y++;
                    return true;
                } else {
                    return false;
                }
            case 'SOUTH':
                if ($this->robot_pos_y - 1 >= 0) {
                    $this->robot_pos_y--;
                    return true;
                } else {
                    return false;
                }
            case 'EAST':
                if ($this->robot_pos_x + 1 <= $this->x_axis_max) {
                    $this->robot_pos_x++;
                    return true;
                } else {
                    return false;
                }
            case 'WEST':
                if ($this->robot_pos_x - 1 >= 0) {
                    $this->robot_pos_x--;
                    return true;
                } else {
                    return false;
                }
        }
    }

    /**
     * Change the orientation of the robot LEFT=>counterclockwise RIGHT=>Clockwise
     * @param $new_value
     */
    public function change_facing(string $new_value)
    {

        if(!in_array($new_value,['LEFT','RIGHT'])) return false;

        switch ($this->robot_facing) {
            case 'NORTH':
                if ($new_value == 'LEFT') $this->robot_facing = 'WEST';
                if ($new_value == 'RIGHT') $this->robot_facing = 'EAST';
                return true;
            case 'WEST':
                if ($new_value == 'LEFT') $this->robot_facing = 'SOUTH';
                if ($new_value == 'RIGHT') $this->robot_facing = 'NORTH';
                return true;
            case 'SOUTH':
                if ($new_value == 'LEFT') $this->robot_facing = 'EAST';
                if ($new_value == 'RIGHT') $this->robot_facing = 'WEST';
                return true;
            case 'EAST':
                if ($new_value == 'LEFT') $this->robot_facing = 'NORTH';
                if ($new_value == 'RIGHT') $this->robot_facing = 'SOUTH';
                return true;
            default:
                return false;
        }
    }

    /**
     * Returns the position of the robot in the board
     */
    public function report()
    {
        return (!is_null($this->robot_pos_x) || !is_null($this->robot_pos_y)) ?
            'Output: ' . $this->robot_pos_x . ',' . $this->robot_pos_y . ',' . $this->robot_facing :
            'Command PLACE not provided';
    }
}
