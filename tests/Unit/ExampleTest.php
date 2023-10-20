<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ToyRobotController;

class ToyRobotTest extends TestCase
{

    public function test_set_place()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));

        $this->assertTrue($toy_robot->set_place(0, 4, 'NORTH'));
        $this->assertFalse($toy_robot->set_place(6, 6, 'NORTH'));
    }

    public function test_move()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));

        $toy_robot->set_place(0, 2, 'NORTH');
        $this->assertTrue($toy_robot->move());
        $toy_robot->set_place(0, 4, 'NORTH');
        $this->assertFalse($toy_robot->move());
        $toy_robot->set_place(0, 2, 'SOUTH');
        $this->assertTrue($toy_robot->move());
        $toy_robot->set_place(0, 0, 'SOUTH');
        $this->assertFalse($toy_robot->move());
        $toy_robot->set_place(0, 0, 'EAST');
        $this->assertTrue($toy_robot->move());
        $toy_robot->set_place(4, 4, 'EAST');
        $this->assertFalse($toy_robot->move());
        $toy_robot->set_place(2, 2, 'WEST');
        $this->assertTrue($toy_robot->move());
        $toy_robot->set_place(0, 0, 'WEST');
        $this->assertFalse($toy_robot->move());
    }

    public function test_change_facing()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));

        $toy_robot->set_place(0, 4, 'NORTH');
        $this->assertTrue($toy_robot->change_facing('LEFT'));
        $toy_robot->set_place(0, 4, 'NORTH');
        $this->assertTrue($toy_robot->change_facing('RIGHT'));

        $toy_robot->set_place(0, 4, 'NORTH');
        $this->assertFalse($toy_robot->change_facing('NOWHERE'));

    }

    public function test_report()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));

        $toy_robot->set_place(0, 4, 'WEST');
        $this->assertEquals('Output: 0,4,WEST', $toy_robot->report());

        $toy_robot->set_place(0, 0, 'WEST');
        $toy_robot->change_facing('LEFT');
        $toy_robot->move();
        $this->assertEquals('Output: 0,0,SOUTH', $toy_robot->report());

        $toy_robot->set_place(0, 0, 'NORTH');
        $toy_robot->change_facing('RIGHT');
        $toy_robot->move();
        $toy_robot->change_facing('LEFT');
        $toy_robot->move();
        $toy_robot->change_facing('RIGHT');
        $this->assertEquals('Output: 1,1,EAST', $toy_robot->report());
    }

    public function test_read_commands_file()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));
        $this->assertEquals('Output: 2,4,EAST',  $toy_robot->read_commands_file('RobotCommands.txt'));
    }

    public function test_read_wrong_commands_file()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));
        $this->assertStringContainsString(
            'not provided',
            $toy_robot->read_commands_file('RobotCommands_wrong_commands.txt'),
            'Command not provided');
    }
}

