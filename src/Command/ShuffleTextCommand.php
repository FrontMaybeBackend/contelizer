<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ShuffleTextCommand extends Command
{
    protected static $defaultName = 'app:shuffle-text';

    protected function configure()
    {
        $this
            ->setDescription('Shuffles letters in words of a text file')
            ->addArgument('input', InputArgument::REQUIRED, 'Input file path')
            ->addArgument('output', InputArgument::OPTIONAL, 'Output file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputPath = $input->getArgument('input');
        $outputPath = $input->getArgument('output') ?? $inputPath . '_shuffled.txt';

        $filesystem = new Filesystem();

        if (!$filesystem->exists($inputPath)) {
            $output->writeln("<error>Plik wejściowy nie istnieje!</error>");
            return Command::FAILURE;
        }

        try {
            $content = file_get_contents($inputPath);
            $processedContent = $this->processText($content);

            $filesystem->dumpFile($outputPath, $processedContent);

            $output->writeln("<info>Plik został przetworzony i zapisany jako: {$outputPath}</info>");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Wystąpił błąd: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }

    private function processText(string $text): string
    {
        // Wzorzec do identyfikacji wyrazów (uwzględnia polskie znaki)
        $pattern = '/\b([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ])([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ])\b/u';

        return preg_replace_callback($pattern, function($matches) {
            // Jeśli wyraz ma mniej niż 4 znaki, nie ma co mieszać
            if (strlen($matches[0]) <= 3) {
                return $matches[0];
            }

            $first = $matches[1];
            $middle = $matches[2];
            $last = $matches[3];

            // Mieszamy środek wyrazu
            $middleChars = mb_str_split($middle, 1, 'UTF-8');
            shuffle($middleChars);
            $shuffledMiddle = implode('', $middleChars);

            return $first . $shuffledMiddle . $last;
        }, $text);
    }
}
