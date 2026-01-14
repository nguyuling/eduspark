<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportHistoryData extends Command
{
    protected $signature = 'import:history-data';
    protected $description = 'Import history data from SQLite export';

    public function handle()
    {
        $file = 'history_data_export.json';
        
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }
        
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        
        if (!$data) {
            $this->error("Failed to decode JSON");
            return 1;
        }
        
        $this->info("Importing history data...\n");
        
        // Tables order (respect foreign keys)
        $importOrder = [
            'quizzes',              // Must come first (parent of quiz_attempts)
            'games',                // Must come first (parent of game_scores, rewards)
            'quiz_attempts',
            'game_scores',
            'messages',
            'leaderboard',
            'rewards',
            'forum_posts',          // Parent of forum_replies
            'forum_replies'
        ];
        
        $totalImported = 0;
        
        foreach ($importOrder as $table) {
            if (!isset($data[$table]) || empty($data[$table])) {
                $this->line("⊘ $table: no data");
                continue;
            }
            
            $rows = $data[$table];
            
            try {
                // Don't truncate - append to existing data
                DB::table($table)->insert($rows);
                
                $this->line("✓ $table: " . count($rows) . " rows imported");
                $totalImported += count($rows);
                
            } catch (\Exception $e) {
                $this->error("✗ $table: " . $e->getMessage());
            }
        }
        
        $this->info("\n✓ Import complete! Total rows: $totalImported");
        return 0;
    }
}
