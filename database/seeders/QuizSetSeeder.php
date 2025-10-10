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
            // Multiple Choice Questions
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the capital of Australia?',
                'options' => ['Sydney', 'Melbourne', 'Canberra', 'Perth'],
                'correct_answer' => 'Canberra',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which river is the longest in the world?',
                'options' => ['Amazon River', 'Nile River', 'Yangtze River', 'Mississippi River'],
                'correct_answer' => 'Nile River',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Mount Everest is located in which mountain range?',
                'options' => ['Andes', 'Rockies', 'Himalayas', 'Alps'],
                'correct_answer' => 'Himalayas',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which country has the largest population in the world?',
                'options' => ['India', 'United States', 'China', 'Indonesia'],
                'correct_answer' => 'China',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the smallest country in the world by land area?',
                'options' => ['Monaco', 'Vatican City', 'San Marino', 'Liechtenstein'],
                'correct_answer' => 'Vatican City',
                'points' => 1
            ],
            // Brief Answer Questions
            [
                'question_type' => 'brief_answer',
                'question' => 'Name the five Great Lakes of North America.',
                'options' => null,
                'correct_answer' => 'Superior, Michigan, Huron, Erie, Ontario',
                'points' => 2
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the name of the largest desert in Asia?',
                'options' => null,
                'correct_answer' => 'Gobi Desert',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Which African country is known as the "Pearl of Africa"?',
                'options' => null,
                'correct_answer' => 'Uganda',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the capital of Argentina?',
                'options' => null,
                'correct_answer' => 'Buenos Aires',
                'points' => 1
            ],
            // True/False Questions
            [
                'question_type' => 'true_false',
                'question' => 'The Amazon River is located in Africa.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Russia spans across two continents: Europe and Asia.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Canada has more lakes than any other country in the world.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The Sahara Desert is the largest desert in the world.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Istanbul is the capital of Turkey.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The United Kingdom is made up of four countries: England, Scotland, Wales, and Northern Ireland.',
                'options' => null,
                'correct_answer' => 'True',
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
            // Multiple Choice Questions
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the chemical symbol for gold?',
                'options' => ['Go', 'Gd', 'Au', 'Ag'],
                'correct_answer' => 'Au',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which planet is known as the Red Planet?',
                'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                'correct_answer' => 'Mars',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the hardest natural substance on Earth?',
                'options' => ['Gold', 'Iron', 'Diamond', 'Platinum'],
                'correct_answer' => 'Diamond',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'How many bones are in the human body?',
                'options' => ['106', '196', '206', '306'],
                'correct_answer' => '206',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the main gas found in the Earth\'s atmosphere?',
                'options' => ['Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Hydrogen'],
                'correct_answer' => 'Nitrogen',
                'points' => 1
            ],
            // Brief Answer Questions
            [
                'question_type' => 'brief_answer',
                'question' => 'What process do plants use to convert sunlight into energy?',
                'options' => null,
                'correct_answer' => 'Photosynthesis',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Name the three states of matter.',
                'options' => null,
                'correct_answer' => 'Solid, Liquid, Gas',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the unit of electrical resistance?',
                'options' => null,
                'correct_answer' => 'Ohm',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Which gland in the human body produces insulin?',
                'options' => null,
                'correct_answer' => 'Pancreas',
                'points' => 1
            ],
            // True/False Questions
            [
                'question_type' => 'true_false',
                'question' => 'Light travels faster than sound.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Humans only use 10% of their brain.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'DNA stands for Deoxyribonucleic Acid.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Water boils at 100 degrees Fahrenheit.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The human body has four lungs.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Electrons are smaller than atoms.',
                'options' => null,
                'correct_answer' => 'True',
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
            // Multiple Choice Questions
            [
                'question_type' => 'multiple_choice',
                'question' => 'How many players are on a soccer team during a match?',
                'options' => ['9', '10', '11', '12'],
                'correct_answer' => '11',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which country won the first ever FIFA World Cup in 1930?',
                'options' => ['Brazil', 'Uruguay', 'Argentina', 'Italy'],
                'correct_answer' => 'Uruguay',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'In tennis, what term is used for a score of 40-40?',
                'options' => ['Advantage', 'Deuce', 'Match Point', 'Set Point'],
                'correct_answer' => 'Deuce',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which NBA player has won the most championships?',
                'options' => ['Michael Jordan', 'Kareem Abdul-Jabbar', 'Bill Russell', 'Magic Johnson'],
                'correct_answer' => 'Bill Russell',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the maximum break possible in snooker?',
                'options' => ['147', '155', '167', '180'],
                'correct_answer' => '147',
                'points' => 1
            ],
            // Brief Answer Questions
            [
                'question_type' => 'brief_answer',
                'question' => 'Which country has won the most FIFA World Cup titles?',
                'options' => null,
                'correct_answer' => 'Brazil',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'In which sport would you perform a "slam dunk"?',
                'options' => null,
                'correct_answer' => 'Basketball',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the length of an Olympic swimming pool in meters?',
                'options' => null,
                'correct_answer' => '50 meters',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Which country hosted the 2020 Summer Olympics?',
                'options' => null,
                'correct_answer' => 'Japan',
                'points' => 1
            ],
            // True/False Questions
            [
                'question_type' => 'true_false',
                'question' => 'Baseball is the national sport of the United States.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'A marathon is 42.195 kilometers long.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'In cricket, a team has 12 players on the field.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Usain Bolt holds the world record for the 100m sprint.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Table tennis originated in England.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'In American football, a touchdown is worth 7 points.',
                'options' => null,
                'correct_answer' => 'False',
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
            // Multiple Choice Questions
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which actor played Jack Dawson in Titanic?',
                'options' => ['Brad Pitt', 'Johnny Depp', 'Leonardo DiCaprio', 'Matt Damon'],
                'correct_answer' => 'Leonardo DiCaprio',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What year was the first Star Wars movie released?',
                'options' => ['1975', '1977', '1979', '1981'],
                'correct_answer' => '1977',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Who directed the movie "Pulp Fiction"?',
                'options' => ['Martin Scorsese', 'Quentin Tarantino', 'Steven Spielberg', 'James Cameron'],
                'correct_answer' => 'Quentin Tarantino',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Which film won the first Academy Award for Best Picture?',
                'options' => ['Sunrise', 'Wings', 'The Broadway Melody', 'All Quiet on the Western Front'],
                'correct_answer' => 'Wings',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the name of the hobbit played by Elijah Wood in Lord of the Rings?',
                'options' => ['Samwise Gamgee', 'Frodo Baggins', 'Peregrin Took', 'Meriadoc Brandybuck'],
                'correct_answer' => 'Frodo Baggins',
                'points' => 1
            ],
            // Brief Answer Questions
            [
                'question_type' => 'brief_answer',
                'question' => 'Which 1994 film features the quote "Life is like a box of chocolates"?',
                'options' => null,
                'correct_answer' => 'Forrest Gump',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Who played the Joker in the 2008 film "The Dark Knight"?',
                'options' => null,
                'correct_answer' => 'Heath Ledger',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the name of the fictional African nation in Black Panther?',
                'options' => null,
                'correct_answer' => 'Wakanda',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Which animated film features the song "Let It Go"?',
                'options' => null,
                'correct_answer' => 'Frozen',
                'points' => 1
            ],
            // True/False Questions
            [
                'question_type' => 'true_false',
                'question' => 'The movie "Avatar" is the highest-grossing film of all time.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Meryl Streep has won the most Academy Awards for acting.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The first movie with sound was "The Jazz Singer" released in 1927.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Steven Spielberg directed the movie "Titanic".',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The Harry Potter film series is based on books by J.K. Rowling.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The movie "The Godfather" won the Academy Award for Best Picture in 1972.',
                'options' => null,
                'correct_answer' => 'True',
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
            // Multiple Choice Questions
            [
                'question_type' => 'multiple_choice',
                'question' => 'Who wrote the play "Romeo and Juliet"?',
                'options' => ['Charles Dickens', 'William Shakespeare', 'Jane Austen', 'Mark Twain'],
                'correct_answer' => 'William Shakespeare',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the largest mammal in the world?',
                'options' => ['African Elephant', 'Blue Whale', 'Giraffe', 'Polar Bear'],
                'correct_answer' => 'Blue Whale',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'In which year did World War II end?',
                'options' => ['1944', '1945', '1946', '1947'],
                'correct_answer' => '1945',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'Who painted the Mona Lisa?',
                'options' => ['Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo'],
                'correct_answer' => 'Leonardo da Vinci',
                'points' => 1
            ],
            [
                'question_type' => 'multiple_choice',
                'question' => 'What is the capital of Japan?',
                'options' => ['Kyoto', 'Osaka', 'Tokyo', 'Seoul'],
                'correct_answer' => 'Tokyo',
                'points' => 1
            ],
            // Brief Answer Questions
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the chemical formula for water?',
                'options' => null,
                'correct_answer' => 'H2O',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Who invented the telephone?',
                'options' => null,
                'correct_answer' => 'Alexander Graham Bell',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'What is the currency of the United Kingdom?',
                'options' => null,
                'correct_answer' => 'Pound Sterling',
                'points' => 1
            ],
            [
                'question_type' => 'brief_answer',
                'question' => 'Which planet is known as the "Red Planet"?',
                'options' => null,
                'correct_answer' => 'Mars',
                'points' => 1
            ],
            // True/False Questions
            [
                'question_type' => 'true_false',
                'question' => 'The Great Wall of China is visible from space with the naked eye.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Bananas are berries, but strawberries are not.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The currency of Switzerland is the Euro.',
                'options' => null,
                'correct_answer' => 'False',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Octopuses have three hearts.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'The novel "1984" was written by George Orwell.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ],
            [
                'question_type' => 'true_false',
                'question' => 'Venus is the hottest planet in our solar system.',
                'options' => null,
                'correct_answer' => 'True',
                'points' => 1
            ]
        ];

        foreach ($questions as $question) {
            Quiz::create(array_merge($question, ['quiz_set_id' => $quizSetId]));
        }
    }
}