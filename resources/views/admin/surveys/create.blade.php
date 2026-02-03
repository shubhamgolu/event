@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Create New Survey</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Surveys
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.surveys.store') }}" method="POST" id="survey-form">
                        @csrf

                        <!-- Event Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3"><i class="fas fa-calendar me-2"></i>Event Selection</h5>
                                
                                @if(isset($event))
                                    <!-- If creating for specific event -->
                                    <div class="alert alert-info">
                                        <strong>Selected Event:</strong> {{ $event->name }}
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                    </div>
                                @else
                                    <!-- If selecting event -->
                                    <div class="mb-3">
                                        <label for="event_id" class="form-label">Select Event *</label>
                                        <select class="form-select @error('event_id') is-invalid @enderror" 
                                                id="event_id" name="event_id" required>
                                            <option value="">-- Select an Event --</option>
                                            @foreach($events as $ev)
                                            <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>
                                                {{ $ev->name }} ({{ $ev->formatted_date }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('event_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Survey Details -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Survey Details</h5>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Survey Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required
                                           placeholder="e.g., Event Feedback Survey">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3"
                                              placeholder="Brief description of the survey...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Settings</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">When to send survey?</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="send_on_checkin" 
                                               id="send_on_checkin" value="1" {{ old('send_on_checkin') ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="send_on_checkin">
                                            <i class="fas fa-sign-in-alt me-1"></i> On Check-in
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="send_on_checkout" 
                                               id="send_on_checkout" value="1" {{ old('send_on_checkout') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="send_on_checkout">
                                            <i class="fas fa-sign-out-alt me-1"></i> On Check-out
                                        </label>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Survey
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Builder -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-question-circle me-2"></i>Survey Questions
                                    <button type="button" class="btn btn-sm btn-success float-end" onclick="addQuestion()">
                                        <i class="fas fa-plus me-1"></i> Add Question
                                    </button>
                                </h5>
                                
                                <div id="questions-container">
                                    <!-- Questions will be added here dynamically -->
                                </div>
                                
                                <div class="text-center mt-3" id="no-questions-message">
                                    <p class="text-muted">No questions added yet. Click "Add Question" to start.</p>
                                </div>
                            </div>
                        </div>

                       

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Create Survey
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Question Template (Hidden) -->
<div id="question-template" class="d-none">
    <div class="card question-card mb-3">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Question <span class="question-number">1</span></h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Question Text *</label>
                        <input type="text" class="form-control question-text" 
                               placeholder="Enter your question here..." required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Question Type *</label>
                        <select class="form-select question-type" onchange="toggleOptions(this)">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="text">Text Input</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input question-required" type="checkbox">
                    <label class="form-check-label">
                        Required Question
                    </label>
                </div>
            </div>
            
            <!-- Options for Multiple Choice -->
            <div class="options-container">
                <div class="mb-2">
                    <label class="form-label">Options (At least 2 required)</label>
                    <div class="options-list">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control option-input" placeholder="Option 1" required>
                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control option-input" placeholder="Option 2" required>
                            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption(this)">
                        <i class="fas fa-plus me-1"></i> Add Option
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Question Builder -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Start with one question
    addQuestion();
});

let questionCount = 0;
let optionCounters = {}; // Track options per question

// Add a new question
function addQuestion() {
    const container = document.getElementById('questions-container');
    const questionIndex = questionCount;
    
    const questionHTML = `
    <div class="card question-card mb-3" id="question-${questionIndex}">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Question ${questionIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(${questionIndex})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Question Text *</label>
                        <input type="text" class="form-control question-text" 
                               name="questions[${questionIndex}][question]" 
                               placeholder="Enter your question..." required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Question Type *</label>
                        <select class="form-select question-type" 
                                name="questions[${questionIndex}][type]"
                                onchange="toggleOptions(${questionIndex})">
                            <option value="multiple_choice" selected>Multiple Choice</option>
                            <option value="text">Text Input</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           name="questions[${questionIndex}][required]" value="1">
                    <label class="form-check-label">
                        Required Question
                    </label>
                </div>
            </div>
            
            <!-- Options Container for Multiple Choice -->
            <div class="options-container" id="options-container-${questionIndex}">
                <div class="mb-2">
                    <label class="form-label">Options (At least 2 required)</label>
                    <div class="options-list" id="options-list-${questionIndex}">
                        <!-- Options will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="addOption(${questionIndex})">
                        <i class="fas fa-plus me-1"></i> Add Option
                    </button>
                </div>
            </div>
        </div>
    </div>`;
    
    container.insertAdjacentHTML('beforeend', questionHTML);
    
    // Initialize options counter for this question
    optionCounters[questionIndex] = 0;
    
    // Add initial two options
    addOption(questionIndex);
    addOption(questionIndex);
    
    questionCount++;
    document.getElementById('no-questions-message').style.display = 'none';
}

// Remove a question
function removeQuestion(questionIndex) {
    const questionElement = document.getElementById(`question-${questionIndex}`);
    if (questionElement) {
        questionElement.remove();
        
        // Re-index remaining questions
        reindexQuestions();
        
        // Update question count
        const remainingQuestions = document.querySelectorAll('.question-card').length;
        if (remainingQuestions === 0) {
            document.getElementById('no-questions-message').style.display = 'block';
            questionCount = 0;
        }
    }
}

// Add an option to a question
function addOption(questionIndex) {
    const optionsList = document.getElementById(`options-list-${questionIndex}`);
    const optionIndex = optionCounters[questionIndex];
    
    const optionHTML = `
    <div class="input-group mb-2 option-item" id="option-${questionIndex}-${optionIndex}">
        <input type="text" class="form-control option-input" 
               name="questions[${questionIndex}][options][${optionIndex}]" 
               placeholder="Option ${optionIndex + 1}" required>
        <button type="button" class="btn btn-outline-danger" 
                onclick="removeOption(${questionIndex}, ${optionIndex})">
            <i class="fas fa-minus"></i>
        </button>
    </div>`;
    
    optionsList.insertAdjacentHTML('beforeend', optionHTML);
    optionCounters[questionIndex]++;
}

// Remove an option
function removeOption(questionIndex, optionIndex) {
    const optionElement = document.getElementById(`option-${questionIndex}-${optionIndex}`);
    if (optionElement) {
        // Check if at least 2 options remain
        const remainingOptions = document.querySelectorAll(`#options-list-${questionIndex} .option-item`).length;
        if (remainingOptions > 2) {
            optionElement.remove();
        } else {
            alert('At least 2 options are required for multiple choice questions.');
        }
    }
}

// Toggle options visibility based on question type
function toggleOptions(questionIndex) {
    const questionElement = document.getElementById(`question-${questionIndex}`);
    const optionsContainer = document.getElementById(`options-container-${questionIndex}`);
    const typeSelect = questionElement.querySelector('.question-type');
    
    if (typeSelect.value === 'multiple_choice') {
        optionsContainer.style.display = 'block';
        // Make all option inputs required
        questionElement.querySelectorAll('.option-input').forEach(input => {
            input.required = true;
        });
    } else {
        optionsContainer.style.display = 'none';
        // Remove required from option inputs
        questionElement.querySelectorAll('.option-input').forEach(input => {
            input.required = false;
        });
    }
}

// Re-index questions after deletion
function reindexQuestions() {
    const questions = document.querySelectorAll('.question-card');
    let newQuestionCount = 0;
    
    questions.forEach((question, index) => {
        const questionIndex = index;
        
        // Update question number display
        question.querySelector('.card-header h6').textContent = `Question ${questionIndex + 1}`;
        
        // Update all input names with new index
        question.querySelectorAll('[name^="questions["]').forEach(input => {
            const oldName = input.name;
            const newName = oldName.replace(/questions\[\d+\]/g, `questions[${questionIndex}]`);
            input.name = newName;
        });
        
        // Update button onclick events
        const header = question.querySelector('.card-header');
        const deleteBtn = header.querySelector('.btn-danger');
        deleteBtn.setAttribute('onclick', `removeQuestion(${questionIndex})`);
        
        // Update type select onchange
        const typeSelect = question.querySelector('.question-type');
        typeSelect.setAttribute('onchange', `toggleOptions(${questionIndex})`);
        
        // Update option buttons
        const addOptionBtn = question.querySelector('.btn-outline-primary');
        addOptionBtn.setAttribute('onclick', `addOption(${questionIndex})`);
        
        // Update IDs
        question.id = `question-${questionIndex}`;
        question.querySelector('.options-container').id = `options-container-${questionIndex}`;
        question.querySelector('.options-list').id = `options-list-${questionIndex}`;
        
        // Re-index options within this question
        const options = question.querySelectorAll('.option-item');
        options.forEach((option, optIndex) => {
            option.id = `option-${questionIndex}-${optIndex}`;
            const input = option.querySelector('input');
            input.name = `questions[${questionIndex}][options][${optIndex}]`;
            input.placeholder = `Option ${optIndex + 1}`;
            
            const removeBtn = option.querySelector('.btn-outline-danger');
            removeBtn.setAttribute('onclick', `removeOption(${questionIndex}, ${optIndex})`);
        });
        
        // Update option counter for this question
        optionCounters[questionIndex] = options.length;
        
        newQuestionCount++;
    });
    
    questionCount = newQuestionCount;
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    let isValid = true;
    let errorMessage = '';
    
    // Check each question
    document.querySelectorAll('.question-card').forEach((question, index) => {
        const questionText = question.querySelector('.question-text').value;
        const questionType = question.querySelector('.question-type').value;
        
        // Check if question has text
        if (!questionText || questionText.trim() === '') {
            isValid = false;
            errorMessage = `Question ${index + 1} is missing text.`;
            return;
        }
        
        // Check multiple choice options
        if (questionType === 'multiple_choice') {
            const options = question.querySelectorAll('.option-input');
            let validOptions = 0;
            
            options.forEach(option => {
                if (option.value && option.value.trim() !== '') {
                    validOptions++;
                }
            });
            
            if (validOptions < 2) {
                isValid = false;
                errorMessage = `Question ${index + 1} (Multiple Choice) needs at least 2 options.`;
                return;
            }
        }
    });
    
    // Check if at least one question exists
    if (document.querySelectorAll('.question-card').length === 0) {
        isValid = false;
        errorMessage = 'Please add at least one question.';
    }
    
    if (!isValid) {
        e.preventDefault();
        alert(errorMessage);
    }
});
</script>

<style>
.question-card {
    border: 1px solid #dee2e6;
}
.options-container .input-group {
    max-width: 400px;
}
</style>
@endsection