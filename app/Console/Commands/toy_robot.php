<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\ToyRobotController;
use Illuminate\Support\Facades\Storage;

class toy_robot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:toy_robot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xplor Exercise';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        do {
            $input = $this->choice(
                'Choose an input',
                [1=>'Promp', 2=>'File', 3=>'Exit'],
                'Promp'
            );

            switch ($input) {
                case 'Promp':
                    $this->promp_input();
                    break;
                case 'File':
                    $this->file_input();
                    break;
            }
        } while ($input != 'Exit');


        return 0;
    }

    private function promp_input()
    {
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));


        do {
            $command = $this->choice(
                'Choose a Command',
                [1 => 'PLACE', 2 => 'MOVE', 3 => 'LEFT', 4 => 'RIGHT', 5 => 'REPORT'],
                'PLACE'
            );

            if ($command == 'PLACE') {
                do {
                    $x = $this->ask('Value for X? (between 0 and ' . (config('toyrobot.x_max')-1) . ')');
                } while (!is_numeric($x) || ($x < 0 || $x > (config('toyrobot.x_max')-1)));
                $this->info('Value for X: ' . $x);

                do {
                    $y = $this->ask('Value for Y? (between 0 and ' . (config('toyrobot.y_max')-1) . ')');
                } while (!is_numeric($y) || ($y < 0 || $y > (config('toyrobot.y_max')-1)));
                $this->info('Value for Y: ' . $y);

                $facing_options = [1 => 'NORTH', 2 => 'SOUTH', 3 => 'EAST', 4 => 'WEST'];
                do {
                    $facing = $this->choice('Choose orientation', $facing_options, 'NORTH');
                } while (!in_array($facing, $facing_options));
                $this->info('Value for Orientation: ' . $facing);

                $command = $command . ' ' . $x . ',' . $y . ',' . $facing;
            }

            $resul = $toy_robot->execute_command($command);
            if (!is_string($resul)) $resul = ($resul) ? 'Done' : 'Ignored';
            $this->line('Command ' . $command . '-> ' . $resul);

        } while (strtoupper($command) != 'REPORT');
    }

    private function file_input()
    {
        $file_list = array_filter(Storage::disk('public')->allFiles(), function ($filename) {
            if (str_contains($filename, 'RobotCommands')) return true;
        });
        // dd($file_list);
        $file_name = $this->choice(
            'Choose a File',
            $file_list,
            $file_list[1]
        );
        // dd($file_name);
        $toy_robot = new ToyRobotController(config('toyrobot.x_max'), config('toyrobot.y_max'));
        $this->line($toy_robot->read_commands_file($file_name));
    }
}
