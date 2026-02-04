<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->title ?? 'Survey' }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .event-info { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        .question { margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .question-text { font-weight: bold; margin-bottom: 10px; }
        .required { color: red; }
        .options { margin-left: 20px; }
        .option { margin: 5px 0; }
        .text-input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .submit-btn { background: #4f46e5; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .submit-btn:hover { background: #4338ca; }
    </style>
</head>
<body>
    @if(isset($alreadySubmitted) && $alreadySubmitted)
        <div class="container">
            <div style="text-align: center; padding: 50px;">
                <div style="font-size: 60px; color: #10b981;">✅</div>
                <h1>Survey Already Submitted</h1>
                <p>You have already completed this survey.</p>
                <p>Your certificate has been generated and emailed to you.</p>
                <a href="{{ route('survey.success', ['token' => $token]) }}" 
                   style="display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; margin-top: 20px;">
                    View Submission Details
                </a>
            </div>
        </div>
    @else
        <div class="container">
            <div class="header">
                <h1>{{ $survey->title ?? 'Survey' }}</h1>
                @if(isset($survey->description))
                    <p>{{ $survey->description }}</p>
                @endif
            </div>
            
            <div class="event-info">
                <h3>Event: {{ $event->name ?? 'Event' }}</h3>
                @if(isset($event->date))
                    <p>Date: {{ date('F j, Y', strtotime($event->date)) }}</p>
                @endif
                <p>Participant: {{ $participant->user->name ?? 'Participant' }}</p>
            </div>
            
            <form id="surveyForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                @if(isset($questions) && count($questions) > 0)
                    @foreach($questions as $index => $question)
                        <div class="question">
                            <div class="question-text">
                                {{ $index + 1 }}. {{ $question['question'] ?? 'Question' }}
                                @if(isset($question['required']) && $question['required'])
                                    <span class="required">*</span>
                                @endif
                            </div>
                            
                            @if(isset($question['type']) && $question['type'] === 'multiple_choice')
                                <div class="options">
                                    @if(isset($question['options']) && is_array($question['options']))
                                        @foreach($question['options'] as $optionIndex => $option)
                                            <div class="option">
                                                <input type="radio" 
                                                       id="q{{ $index }}_o{{ $optionIndex }}" 
                                                       name="answers[question_{{ $index }}]" 
                                                       value="{{ $option }}"
                                                       {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                                <label for="q{{ $index }}_o{{ $optionIndex }}">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @else
                                <textarea name="answers[question_{{ $index }}]" 
                                          class="text-input" 
                                          rows="3"
                                          {{ isset($question['required']) && $question['required'] ? 'required' : '' }}
                                          placeholder="Your answer..."></textarea>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="question">
                        <p>No questions available for this survey.</p>
                    </div>
                @endif
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="submit-btn">Submit Survey</button>
                </div>
            </form>
            
            <div id="message" style="display: none; margin-top: 20px; padding: 15px; border-radius: 5px;"></div>
        </div>
    @endif
    
    @if(!isset($alreadySubmitted) || !$alreadySubmitted)
    <script>
        document.getElementById('surveyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = document.querySelector('.submit-btn');
            const messageDiv = document.getElementById('message');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            messageDiv.style.display = 'none';
            
            try {
                const response = await fetch('{{ route("survey.submit", ["token" => $token]) }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.style.backgroundColor = '#d1fae5';
                    messageDiv.style.color = '#065f46';
                    messageDiv.textContent = data.message;
                    messageDiv.style.display = 'block';
                    
                    // Redirect to success page
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, 2000);
                } else {
                    messageDiv.style.backgroundColor = '#fee2e2';
                    messageDiv.style.color = '#991b1b';
                    messageDiv.textContent = data.message || 'An error occurred';
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Survey';
                }
            } catch (error) {
                messageDiv.style.backgroundColor = '#fee2e2';
                messageDiv.style.color = '#991b1b';
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Survey';
            }
        });
    </script>
    @endif
</body>
</html>