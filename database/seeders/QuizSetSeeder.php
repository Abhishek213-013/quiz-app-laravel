<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizSet;
use App\Models\Quiz;

class QuizSetSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        Quiz::query()->delete();
        QuizSet::query()->delete();

        // 1. Geography Quiz Set
        $geographySet = QuizSet::create([
            'name' => 'World Geography Challenge',
            'category' => 'Geography',
            'description' => 'Test your knowledge of countries, capitals, and geographical features around the world',
            'time_limit' => 900, // 15 minutes
            'question_count' => 15
        ]);

        $this->createGeographyQuestions($geographySet->id);

        // 2. Science Quiz Set
        $scienceSet = QuizSet::create([
            'name' => 'General Science Quiz',
            'category' => 'Science',
            'description' => 'Explore questions from physics, chemistry, biology, and earth sciences',
            'time_limit' => 900,
            'question_count' => 15
        ]);

        $this->createScienceQuestions($scienceSet->id);

        // 3. Sports Quiz Set
        $sportsSet = QuizSet::create([
            'name' => 'Sports Trivia',
            'category' => 'Sports',
            'description' => 'Test your knowledge of various sports, athletes, and championships',
            'time_limit' => 900,
            'question_count' => 15
        ]);

        $this->createSportsQuestions($sportsSet->id);

        // 4. Films Quiz Set
        $filmsSet = QuizSet::create([
            'name' => 'Movie Mania',
            'category' => 'Films',
            'description' => 'How well do you know movies, actors, and famous film quotes?',
            'time_limit' => 900,
            'question_count' => 15
        ]);

        $this->createFilmsQuestions($filmsSet->id);

        // 5. Mixed Trivia Quiz Set
        $mixedSet = QuizSet::create([
            'name' => 'Mixed Trivia Challenge',
            'category' => 'Mixed',
            'description' => 'A mix of questions from history, literature, music, and general knowledge',
            'time_limit' => 900,
            'question_count' => 15
        ]);

        $this->createMixedQuestions($mixedSet->id);
    }

    private function createGeographyQuestions($quizSetId)
    {
        $questions = [
            [
                'question' => 'What is the capital of Australia?',
                'options' => ['Sydney', 'Melbourne', 'Canberra', 'Perth'],
                'correct_answer' => 'Canberra',
                'points' => 1
            ],
            [
                'question' => 'Which river is the longest in the world?',
                'options' => ['Amazon River', 'Nile River', 'Yangtze River', 'Mississippi River'],
                'correct_answer' => 'Nile River',
                'points' => 1
            ],
            [
                'question' => 'Mount Everest is located in which mountain range?',
                'options' => ['Andes', 'Rockies', 'Himalayas', 'Alps'],
                'correct_answer' => 'Himalayas',
                'points' => 1
            ],
            [
                'question' => 'Which country has the largest population in the world?',
                'options' => ['India', 'United States', 'China', 'Indonesia'],
                'correct_answer' => 'China',
                'points' => 1
            ],
            [
                'question' => 'What is the smallest country in the world by land area?',
                'options' => ['Monaco', 'Vatican City', 'San Marino', 'Liechtenstein'],
                'correct_answer' => 'Vatican City',
                'points' => 1
            ],
            [
                'question' => 'Which desert is the largest in the world?',
                'options' => ['Sahara Desert', 'Arabian Desert', 'Gobi Desert', 'Antarctic Desert'],
                'correct_answer' => 'Antarctic Desert',
                'points' => 1
            ],
            [
                'question' => 'What is the capital of Brazil?',
                'options' => ['Rio de Janeiro', 'São Paulo', 'Brasília', 'Salvador'],
                'correct_answer' => 'Brasília',
                'points' => 1
            ],
            [
                'question' => 'Which ocean is the largest?',
                'options' => ['Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean'],
                'correct_answer' => 'Pacific Ocean',
                'points' => 1
            ],
            [
                'question' => 'What is the official language of Brazil?',
                'options' => ['Spanish', 'Portuguese', 'English', 'French'],
                'correct_answer' => 'Portuguese',
                'points' => 1
            ],
            [
                'question' => 'Which country is known as the Land of the Rising Sun?',
                'options' => ['China', 'Thailand', 'Japan', 'South Korea'],
                'correct_answer' => 'Japan',
                'points' => 1
            ],
            [
                'question' => 'What is the capital of Canada?',
                'options' => ['Toronto', 'Vancouver', 'Ottawa', 'Montreal'],
                'correct_answer' => 'Ottawa',
                'points' => 1
            ],
            [
                'question' => 'Which African country was never colonized?',
                'options' => ['Ethiopia', 'Kenya', 'Nigeria', 'South Africa'],
                'correct_answer' => 'Ethiopia',
                'points' => 1
            ],
            [
                'question' => 'What is the longest river in South America?',
                'options' => ['Orinoco River', 'Paraná River', 'Amazon River', 'São Francisco River'],
                'correct_answer' => 'Amazon River',
                'points' => 1
            ],
            [
                'question' => 'Which European country has the most islands?',
                'options' => ['Greece', 'Sweden', 'Norway', 'Finland'],
                'correct_answer' => 'Sweden',
                'points' => 1
            ],
            [
                'question' => 'What is the highest mountain in Africa?',
                'options' => ['Mount Kenya', 'Mount Kilimanjaro', 'Mount Stanley', 'Mount Meru'],
                'correct_answer' => 'Mount Kilimanjaro',
                'points' => 1
            ]
        ];

        foreach ($questions as $question) {
            Quiz::create(array_merge($question, ['quiz_set_id' => $quizSetId]));
        }
    }

    private function createScienceQuestions($quizSetId)
    {
        $questions = [
            [
                'question' => 'What is the chemical symbol for gold?',
                'options' => ['Go', 'Gd', 'Au', 'Ag'],
                'correct_answer' => 'Au',
                'points' => 1
            ],
            [
                'question' => 'Which planet is known as the Red Planet?',
                'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                'correct_answer' => 'Mars',
                'points' => 1
            ],
            [
                'question' => 'What is the hardest natural substance on Earth?',
                'options' => ['Gold', 'Iron', 'Diamond', 'Platinum'],
                'correct_answer' => 'Diamond',
                'points' => 1
            ],
            [
                'question' => 'How many bones are in the human body?',
                'options' => ['106', '196', '206', '306'],
                'correct_answer' => '206',
                'points' => 1
            ],
            [
                'question' => 'What is the main gas found in the Earth\'s atmosphere?',
                'options' => ['Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Hydrogen'],
                'correct_answer' => 'Nitrogen',
                'points' => 1
            ],
            [
                'question' => 'Which of these is NOT a type of blood cell?',
                'options' => ['Red blood cell', 'White blood cell', 'Blue blood cell', 'Platelet'],
                'correct_answer' => 'Blue blood cell',
                'points' => 1
            ],
            [
                'question' => 'What is the speed of light in vacuum?',
                'options' => ['299,792,458 m/s', '150,000,000 m/s', '1,080,000,000 km/h', '671,000,000 mph'],
                'correct_answer' => '299,792,458 m/s',
                'points' => 1
            ],
            [
                'question' => 'Which element has the atomic number 1?',
                'options' => ['Helium', 'Oxygen', 'Hydrogen', 'Carbon'],
                'correct_answer' => 'Hydrogen',
                'points' => 1
            ],
            [
                'question' => 'What is the largest organ in the human body?',
                'options' => ['Liver', 'Brain', 'Skin', 'Heart'],
                'correct_answer' => 'Skin',
                'points' => 1
            ],
            [
                'question' => 'Which scientist developed the theory of relativity?',
                'options' => ['Isaac Newton', 'Albert Einstein', 'Stephen Hawking', 'Galileo Galilei'],
                'correct_answer' => 'Albert Einstein',
                'points' => 1
            ],
            [
                'question' => 'What is H2O more commonly known as?',
                'options' => ['Hydrogen Peroxide', 'Water', 'Oxygen Gas', 'Heavy Water'],
                'correct_answer' => 'Water',
                'points' => 1
            ],
            [
                'question' => 'Which planet has the most moons?',
                'options' => ['Jupiter', 'Saturn', 'Uranus', 'Neptune'],
                'correct_answer' => 'Saturn',
                'points' => 1
            ],
            [
                'question' => 'What is the process by which plants make their food?',
                'options' => ['Respiration', 'Photosynthesis', 'Transpiration', 'Germination'],
                'correct_answer' => 'Photosynthesis',
                'points' => 1
            ],
            [
                'question' => 'Which gas do plants absorb from the atmosphere?',
                'options' => ['Oxygen', 'Nitrogen', 'Carbon Dioxide', 'Hydrogen'],
                'correct_answer' => 'Carbon Dioxide',
                'points' => 1
            ],
            [
                'question' => 'What is the study of fossils called?',
                'options' => ['Archaeology', 'Paleontology', 'Geology', 'Anthropology'],
                'correct_answer' => 'Paleontology',
                'points' => 1
            ]
        ];

        foreach ($questions as $question) {
            Quiz::create(array_merge($question, ['quiz_set_id' => $quizSetId]));
        }
    }

    private function createSportsQuestions($quizSetId)
    {
        $questions = [
            [
                'question' => 'How many players are on a soccer team during a match?',
                'options' => ['9', '10', '11', '12'],
                'correct_answer' => '11',
                'points' => 1
            ],
            [
                'question' => 'Which country won the first ever FIFA World Cup in 1930?',
                'options' => ['Brazil', 'Uruguay', 'Argentina', 'Italy'],
                'correct_answer' => 'Uruguay',
                'points' => 1
            ],
            [
                'question' => 'In tennis, what term is used for a score of 40-40?',
                'options' => ['Advantage', 'Deuce', 'Match Point', 'Set Point'],
                'correct_answer' => 'Deuce',
                'points' => 1
            ],
            [
                'question' => 'Which NBA player has won the most championships?',
                'options' => ['Michael Jordan', 'Kareem Abdul-Jabbar', 'Bill Russell', 'Magic Johnson'],
                'correct_answer' => 'Bill Russell',
                'points' => 1
            ],
            [
                'question' => 'What is the maximum break possible in snooker?',
                'options' => ['147', '155', '167', '180'],
                'correct_answer' => '147',
                'points' => 1
            ],
            [
                'question' => 'Which country hosted the 2016 Summer Olympics?',
                'options' => ['China', 'Brazil', 'Russia', 'United Kingdom'],
                'correct_answer' => 'Brazil',
                'points' => 1
            ],
            [
                'question' => 'In baseball, how many strikes make an out?',
                'options' => ['2', '3', '4', '5'],
                'correct_answer' => '3',
                'points' => 1
            ],
            [
                'question' => 'Which sport uses the term "love" to mean zero?',
                'options' => ['Golf', 'Tennis', 'Cricket', 'Badminton'],
                'correct_answer' => 'Tennis',
                'points' => 1
            ],
            [
                'question' => 'How long is a marathon in kilometers?',
                'options' => ['40.2 km', '41.195 km', '42.195 km', '43.2 km'],
                'correct_answer' => '42.195 km',
                'points' => 1
            ],
            [
                'question' => 'Which boxer was known as "The Greatest"?',
                'options' => ['Mike Tyson', 'Muhammad Ali', 'Sugar Ray Leonard', 'Joe Frazier'],
                'correct_answer' => 'Muhammad Ali',
                'points' => 1
            ],
            [
                'question' => 'In cricket, how many balls are in an over?',
                'options' => ['4', '5', '6', '7'],
                'correct_answer' => '6',
                'points' => 1
            ],
            [
                'question' => 'Which country invented the sport of golf?',
                'options' => ['England', 'United States', 'Scotland', 'Ireland'],
                'correct_answer' => 'Scotland',
                'points' => 1
            ],
            [
                'question' => 'What is the diameter of a basketball hoop in inches?',
                'options' => ['16 inches', '18 inches', '20 inches', '24 inches'],
                'correct_answer' => '18 inches',
                'points' => 1
            ],
            [
                'question' => 'Which athlete has won the most Olympic gold medals?',
                'options' => ['Usain Bolt', 'Carl Lewis', 'Michael Phelps', 'Larisa Latynina'],
                'correct_answer' => 'Michael Phelps',
                'points' => 1
            ],
            [
                'question' => 'In American football, how many points is a touchdown worth?',
                'options' => ['5', '6', '7', '8'],
                'correct_answer' => '6',
                'points' => 1
            ]
        ];

        foreach ($questions as $question) {
            Quiz::create(array_merge($question, ['quiz_set_id' => $quizSetId]));
        }
    }

    private function createFilmsQuestions($quizSetId)
    {
        $questions = [
            [
                'question' => 'Which actor played Jack Dawson in Titanic?',
                'options' => ['Brad Pitt', 'Johnny Depp', 'Leonardo DiCaprio', 'Matt Damon'],
                'correct_answer' => 'Leonardo DiCaprio',
                'points' => 1
            ],
            [
                'question' => 'What year was the first Star Wars movie released?',
                'options' => ['1975', '1977', '1979', '1981'],
                'correct_answer' => '1977',
                'points' => 1
            ],
            [
                'question' => 'Which movie features the quote: "You talking to me?"',
                'options' => ['Scarface', 'Goodfellas', 'Taxi Driver', 'The Godfather'],
                'correct_answer' => 'Taxi Driver',
                'points' => 1
            ],
            [
                'question' => 'Who directed the movie "Pulp Fiction"?',
                'options' => ['Martin Scorsese', 'Quentin Tarantino', 'Steven Spielberg', 'James Cameron'],
                'correct_answer' => 'Quentin Tarantino',
                'points' => 1
            ],
            [
                'question' => 'Which film won the first Academy Award for Best Picture?',
                'options' => ['Sunrise', 'Wings', 'The Broadway Melody', 'All Quiet on the Western Front'],
                'correct_answer' => 'Wings',
                'points' => 1
            ],
            [
                'question' => 'What is the name of the hobbit played by Elijah Wood in Lord of the Rings?',
                'options' => ['Samwise Gamgee', 'Frodo Baggins', 'Peregrin Took', 'Meriadoc Brandybuck'],
                'correct_answer' => 'Frodo Baggins',
                'points' => 1
            ],
            [
                'question' => 'Which actress played Hermione Granger in the Harry Potter films?',
                'options' => ['Emma Roberts', 'Emma Stone', 'Emma Watson', 'Emma Thompson'],
                'correct_answer' => 'Emma Watson',
                'points' => 1
            ],
            [
                'question' => 'What is the highest-grossing film of all time?',
                'options' => ['Avatar', 'Avengers: Endgame', 'Titanic', 'Star Wars: The Force Awakens'],
                'correct_answer' => 'Avatar',
                'points' => 1
            ],
            [
                'question' => 'Which movie features a dance called the "twerking" scene with Miley Cyrus?',
                'options' => ['The Last Song', 'LOL', 'So Undercover', 'None of these'],
                'correct_answer' => 'None of these',
                'points' => 1
            ],
            [
                'question' => 'Who composed the music for Star Wars?',
                'options' => ['Hans Zimmer', 'John Williams', 'James Horner', 'Danny Elfman'],
                'correct_answer' => 'John Williams',
                'points' => 1
            ],
            [
                'question' => 'Which animated movie features the song "Let It Go"?',
                'options' => ['Tangled', 'Moana', 'Frozen', 'The Princess and the Frog'],
                'correct_answer' => 'Frozen',
                'points' => 1
            ],
            [
                'question' => 'What is the name of the fictional country in Black Panther?',
                'options' => ['Zamunda', 'Wakanda', 'Genovia', 'Latveria'],
                'correct_answer' => 'Wakanda',
                'points' => 1
            ],
            [
                'question' => 'Which actor has appeared in the most Best Picture Oscar winners?',
                'options' => ['Jack Nicholson', 'Meryl Streep', 'Robert De Niro', 'Tom Hanks'],
                'correct_answer' => 'Jack Nicholson',
                'points' => 1
            ],
            [
                'question' => 'What was the first feature-length animated movie?',
                'options' => ['Snow White and the Seven Dwarfs', 'Steamboat Willie', 'Fantasia', 'Bambi'],
                'correct_answer' => 'Snow White and the Seven Dwarfs',
                'points' => 1
            ],
            [
                'question' => 'Which movie won the most Oscars without winning Best Picture?',
                'options' => ['Cabaret', 'The Color Purple', 'La La Land', 'Roma'],
                'correct_answer' => 'Cabaret',
                'points' => 1
            ]
        ];

        foreach ($questions as $question) {
            Quiz::create(array_merge($question, ['quiz_set_id' => $quizSetId]));
        }
    }

    private function createMixedQuestions($quizSetId)
    {
        $questions = [
            [
                'question' => 'Who wrote the play "Romeo and Juliet"?',
                'options' => ['Charles Dickens', 'William Shakespeare', 'Jane Austen', 'Mark Twain'],
                'correct_answer' => 'William Shakespeare',
                'points' => 1
            ],
            [
                'question' => 'What is the largest mammal in the world?',
                'options' => ['African Elephant', 'Blue Whale', 'Giraffe', 'Polar Bear'],
                'correct_answer' => 'Blue Whale',
                'points' => 1
            ],
            [
                'question' => 'In which year did World War II end?',
                'options' => ['1944', '1945', '1946', '1947'],
                'correct_answer' => '1945',
                'points' => 1
            ],
            [
                'question' => 'Who painted the Mona Lisa?',
                'options' => ['Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo'],
                'correct_answer' => 'Leonardo da Vinci',
                'points' => 1
            ],
            [
                'question' => 'What is the capital of Japan?',
                'options' => ['Kyoto', 'Osaka', 'Tokyo', 'Seoul'],
                'correct_answer' => 'Tokyo',
                'points' => 1
            ],
            [
                'question' => 'Which instrument has 88 keys?',
                'options' => ['Guitar', 'Violin', 'Piano', 'Organ'],
                'correct_answer' => 'Piano',
                'points' => 1
            ],
            [
                'question' => 'What is the smallest prime number?',
                'options' => ['0', '1', '2', '3'],
                'correct_answer' => '2',
                'points' => 1
            ],
            [
                'question' => 'Who discovered penicillin?',
                'options' => ['Marie Curie', 'Alexander Fleming', 'Louis Pasteur', 'Robert Koch'],
                'correct_answer' => 'Alexander Fleming',
                'points' => 1
            ],
            [
                'question' => 'What is the currency of Switzerland?',
                'options' => ['Euro', 'Swiss Franc', 'Swiss Dollar', 'Swiss Lira'],
                'correct_answer' => 'Swiss Franc',
                'points' => 1
            ],
            [
                'question' => 'Which planet is closest to the Sun?',
                'options' => ['Venus', 'Earth', 'Mars', 'Mercury'],
                'correct_answer' => 'Mercury',
                'points' => 1
            ],
            [
                'question' => 'What is the main ingredient in guacamole?',
                'options' => ['Tomato', 'Avocado', 'Pepper', 'Onion'],
                'correct_answer' => 'Avocado',
                'points' => 1
            ],
            [
                'question' => 'Who is known as the Father of Computers?',
                'options' => ['Alan Turing', 'Charles Babbage', 'Bill Gates', 'Steve Jobs'],
                'correct_answer' => 'Charles Babbage',
                'points' => 1
            ],
            [
                'question' => 'What is the largest organ in the human body?',
                'options' => ['Liver', 'Brain', 'Skin', 'Heart'],
                'correct_answer' => 'Skin',
                'points' => 1
            ],
            [
                'question' => 'Which country gifted the Statue of Liberty to the United States?',
                'options' => ['Spain', 'France', 'United Kingdom', 'Italy'],
                'correct_answer' => 'France',
                'points' => 1
            ],
            [
                'question' => 'What is the most spoken language in the world?',
                'options' => ['English', 'Spanish', 'Mandarin Chinese', 'Hindi'],
                'correct_answer' => 'Mandarin Chinese',
                'points' => 1
            ]
        ];

        foreach ($questions as $question) {
            Quiz::create(array_merge($question, ['quiz_set_id' => $quizSetId]));
        }
    }
}