// Quiz Functionality - moved outside DOMContentLoaded to be globally accessible
let currentQuestion = 0;

// Use questions from database if available, otherwise use fallback questions
let questions = typeof quizQuestionsFromDB !== 'undefined' && quizQuestionsFromDB.length > 0 
  ? quizQuestionsFromDB 
  : [
    {
        question: "Which of these actions saves the most energy in your home?",
        options: [
            "Using LED light bulbs",
            "Proper insulation of walls and roof",
            "Unplugging unused devices",
            "Using a smart thermostat"
        ],
        correct: 1
    },
    {
        question: "What is the most effective way to reduce plastic waste?",
        options: [
            "Recycling plastic bottles",
            "Using reusable containers",
            "Buying products with less packaging",
            "Using biodegradable plastics"
        ],
        correct: 1
    },
    {
        question: "Which practice best supports local ecology?",
        options: [
            "Growing native plants",
            "Installing a water fountain",
            "Using chemical fertilizers",
            "Removing fallen leaves"
        ],
        correct: 0
    },
    {
        question: "What's the most eco-friendly way to dry clothes?",
        options: [
            "Using a dryer on eco mode",
            "Air drying outside",
            "Using a dehumidifier",
            "Quick dry cycle"
        ],
        correct: 1
    },
    {
        question: "Which transportation method has the lowest carbon footprint?",
        options: [
            "Electric car",
            "Hybrid vehicle",
            "Bicycle",
            "Bus"
        ],
        correct: 2
    }
];

// Keep track of user's answers
let userAnswers = new Array(questions.length).fill(null);
let score = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Share Tip Modal
    const shareNewTipBtn = document.getElementById('shareNewTip');
    const shareTipModal = document.getElementById('shareTipModal');
    const closeModalBtn = shareTipModal.querySelector('.close-modal');
    const shareTipForm = document.getElementById('shareTipForm');

    shareNewTipBtn.addEventListener('click', () => {
        shareTipModal.classList.add('active');
    });

    closeModalBtn.addEventListener('click', closeShareTipModal);

    // Close modal when clicking outside
    shareTipModal.addEventListener('click', (e) => {
        if (e.target === shareTipModal) {
            closeShareTipModal();
        }
    });

    // Handle form submission
    shareTipForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Get form values
        const title = document.getElementById('tipTitle').value;
        const content = document.getElementById('tipContent').value;
        const category = document.getElementById('tipCategory').value;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('tip_title', title);
        formData.append('tip_content', content);
        formData.append('tip_category', category);
        
        try {
            // Send to backend
            const response = await fetch('submit_tip.php', {
                method: 'POST',
                body: formData
            });
            
            // Check if response is OK
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                showSuccessMessage(result.message);
                
                // Reset form and close modal
                shareTipForm.reset();
                closeShareTipModal();
                
                // Reload page to show new tip
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message + '\nPlease check the browser console for details.');
        }
    });

    // Tab Navigation
    const navButtons = document.querySelectorAll('.tips-nav button');
    const sections = document.querySelectorAll('.tips-section');

    navButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Update active button
            navButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Show corresponding section
            const sectionId = button.dataset.section;
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === sectionId) {
                    section.classList.add('active');
                }
            });
        });
    });

    // Handle option selection - set up event listeners
    setupQuizListeners();
    
    // Initialize quiz if questions exist
    if (questions && questions.length > 0) {
        // Reset quiz state
        currentQuestion = 0;
        userAnswers = new Array(questions.length).fill(null);
        score = 0;
        // Load first question
        updateQuiz();
    }
});

function setupQuizListeners() {
    const options = document.querySelectorAll('.option');
    options.forEach((option, index) => {
        option.addEventListener('click', () => {
            // Remove selection and feedback from other options
            const allOptions = document.querySelectorAll('.option');
            allOptions.forEach(opt => {
                opt.classList.remove('selected', 'correct', 'wrong');
            });
            
            // Select clicked option
            option.classList.add('selected');
            
            // Save user's answer
            userAnswers[currentQuestion] = index;
            
            // Show immediate feedback
            const correctIndex = questions[currentQuestion].correct;
            if (index === correctIndex) {
                option.classList.add('correct');
            } else {
                option.classList.add('wrong');
                // Also highlight the correct answer
                allOptions[correctIndex].classList.add('correct');
            }
        });
    });
}

function nextQuestion() {
    const options = document.querySelectorAll('.option');
    if (!Array.from(options).some(option => option.classList.contains('selected'))) {
        alert('Please select an answer before proceeding');
        return;
    }

    currentQuestion++;
    if (currentQuestion >= questions.length) {
        showResults();
        return;
    }

    updateQuiz();
}

function updateQuiz() {
    const quizCard = document.querySelector('.quiz-card');
    const progressBar = document.querySelector('.progress-fill');
    const questionNumber = document.querySelector('.quiz-progress p');

    // Update question
    quizCard.querySelector('.question').textContent = questions[currentQuestion].question;
    
    // Create or update options
    const optionsContainer = quizCard.querySelector('.options');
    optionsContainer.innerHTML = ''; // Clear existing options
    
    questions[currentQuestion].options.forEach((optionText, index) => {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'option';
        optionDiv.textContent = optionText;
        optionsContainer.appendChild(optionDiv);
    });

    // Re-attach event listeners to new options
    setupQuizListeners();

    // Update progress
    const progress = ((currentQuestion + 1) / questions.length) * 100;
    progressBar.style.width = `${progress}%`;
    questionNumber.textContent = `Question ${currentQuestion + 1} of ${questions.length}`;

    // Update button text for last question
    const nextButton = document.querySelector('.quiz-progress .btn');
    if (currentQuestion === questions.length - 1) {
        nextButton.textContent = 'Finish Quiz';
    }
}

function showResults() {
    const score = userAnswers.reduce((total, answer, index) => {
        return total + (answer === questions[index].correct ? 1 : 0);
    }, 0);

    const quizContainer = document.querySelector('.quiz-container');
    quizContainer.innerHTML = `
        <div class="quiz-card">
            <h2>Quiz Results</h2>
            <p>You scored ${score} out of ${questions.length}!</p>
            <div class="quiz-feedback">
                <p>${getScoreFeedback(score, questions.length)}</p>
            </div>
            <button class="btn btn-primary" onclick="location.reload()">Try Again</button>
        </div>
    `;
}

function getScoreFeedback(score, total) {
    const percentage = (score / total) * 100;
    if (percentage >= 80) {
        return "Excellent! You're an eco-warrior! Keep spreading awareness about sustainable living.";
    } else if (percentage >= 60) {
        return "Good job! You have a solid understanding of eco-friendly practices. Keep learning!";
    } else {
        return "Thanks for taking the quiz! Check out our tips sections to learn more about sustainable living.";
    }
}

function closeShareTipModal() {
    document.getElementById('shareTipModal').classList.remove('active');
    document.getElementById('shareTipForm').reset();
}

function createTipCard(tip) {
    const div = document.createElement('div');
    div.className = 'tip-card';
    div.innerHTML = `
        <h3>${tip.title}</h3>
        <p class="tip-content">${tip.content}</p>
        <div class="tip-meta">
            <span class="tip-author">By: ${tip.author}</span>
            <span class="tip-category">Category: ${tip.category === 'energy' ? 'Energy Saving' : 'Green Tips'}</span>
        </div>
    `;
    return div;
}

function showSuccessMessage(message) {
    // Create success message element if it doesn't exist
    let successMsg = document.querySelector('.success-message');
    if (!successMsg) {
        successMsg = document.createElement('div');
        successMsg.className = 'success-message';
        document.querySelector('.share-tip-container').appendChild(successMsg);
    }
    
    successMsg.textContent = message;
    successMsg.classList.add('active');
    
    // Remove after 3 seconds
    setTimeout(() => {
        successMsg.classList.remove('active');
    }, 3000);
}
