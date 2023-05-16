<?php 

namespace App\Helpers;

class DockerHelper
{

    public static function checkContainerStatus(string $container) : array
    {
        
        exec('docker ps -a', $output);
        // Parse the output and check if the container is present
        $containerRunning = [
            'presence' => false,
            'running' => false
        ];

        foreach ($output as $line) {
            // Split each line of the output by whitespace
            $parts = preg_split('/\s+/', $line);
            // Check if the container name or ID matches
            if (in_array($container, $parts)) {
                if(strpos($line, 'Up') !== false){
                    $containerRunning['running'] = true;
                }
                $containerRunning['presence'] = true;
                break;
            }
        }

        return $containerRunning;
    }

    public static function stopContainer(string $name) :void
    {
        exec("docker stop {$name}", $output);
        //exec("docker rm {$name}", $output);
    }

    public static function runContainer(array $containerData) : array
    {
        if ($containerData['presence'] === true) {
            exec("docker start {$containerData['name']}", $output);
        } else {
            $envs = "";
            $hosts = "";
            foreach ($containerData['environments'] as $envName => $envValue) {
                $envs .= " -e {$envName}={$envValue}";
            }
            if(env('APP_ENV') === 'local'){
                $hosts = ' --add-host=artisan.local:172.18.0.1';
            }
            exec("docker run -d{$hosts} --name {$containerData['name']}{$envs} {$containerData['image']}", $output);
        }
        
        return $output;
    }

    public static function getContainerIP(string $containerName) : string
    {
        //dd('eee');
        $output = shell_exec("docker inspect {$containerName} | grep IPAddress");
        preg_match('/"IPAddress":\s*"([^"]+)"/', $output, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }
}