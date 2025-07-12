<!-- Footer -->
<footer class="py-5">
  <div class="container">
    <div class="row align-items-center justify-content-xl-between">
      <div class="col-xl-6">
        <div class="copyright text-center text-xl-left text-muted">
          &copy; <?php echo date('Y'); ?> - SE102 POS System Project
        </div>
      </div>
      <div class="col-xl-6">
        <ul class="nav nav-footer justify-content-center justify-content-xl-end">
          <li class="nav-item">
            <a href="" class="nav-link" target="_blank">Henry's Hardware Store</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>

<!-- Chat Button -->
<!-- Add Font Awesome for chat icon -->
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<!-- Chat Button with icon -->
<div id="chat-btn" style="position: fixed; bottom: 20px; right: 20px; background-color: #007bff; color: white; padding: 12px 20px; border-radius: 50px; cursor: pointer; font-size: 16px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); transition: transform 0.3s ease; display: flex; align-items: center;">
  <i class="fas fa-comment-dots" style="margin-right: 8px;"></i> Chat with us!
</div>


<!-- Chat Window (hidden initially) -->
<div id="chat-window" style="display: none; position: fixed; bottom: -450px; right: 20px; width: 350px; height: 450px; background-color: #ffffff; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0px 0px 20px rgba(0,0,0,0.1); transition: bottom 0.5s ease-in-out;">
  <div style="padding: 15px; background-color: #007bff; color: white; border-radius: 10px 10px 0 0;">
    <strong>Support Chat</strong>
    <span style="float: right; cursor: pointer;" onclick="closeChat()">X</span>
  </div>
  <div id="chat-content" style="padding: 15px; height: calc(100% - 85px); overflow-y: auto; font-family: 'Arial', sans-serif;">
    <div id="pre-questions" style="display: flex; flex-direction: column; gap: 10px;">
      <button class="pre-question" onclick="sendPredefinedAnswer('What can you offer for us?')">What can you offer for us?</button>
      <button class="pre-question" onclick="sendPredefinedAnswer('Do you support GCash, Cash on payments?')">Do you support GCash payments?</button>
      <button class="pre-question" onclick="sendPredefinedAnswer('Do you support Cash on Delivery payments?')">Do you support Cash on Delivery payments?</button>
      <button class="pre-question" onclick="sendPredefinedAnswer('How long it takes to deliver the items?')">How long it takes to deliver the items?</button>
      <button class="pre-question" onclick="sendPredefinedAnswer('How can I contact customer support?')">How can I contact customer support?</button>
      <br>
    </div>
    <div id="back-to-questions" style="display: none; padding: 10px; text-align: center;">
      <button onclick="showPreQuestions()" style="background-color: #007bff; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Back to Questions</button>
    </div>
  </div>

  <!-- Input field and send button -->
  <div style="padding: 10px; border-top: 1px solid #ddd;">
    <input type="text" id="chat-input" placeholder="Type your message..." style="width: calc(105% - 20px); padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;">
    <button onclick="sendMessage()" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; margin-top: 5px; cursor: pointer;">Send</button>
  </div>
</div>


<script>
  // Open chat window with animation
  document.getElementById('chat-btn').onclick = function () {
    var chatWindow = document.getElementById('chat-window');
    chatWindow.style.display = 'block';  // Make it visible
    setTimeout(function () {
      chatWindow.style.bottom = '80px';  // Animate it up
    }, 10);  // Slight delay to trigger transition
  };

  function sendPredefinedAnswer(answer) {
    var chatContent = document.getElementById('chat-content');

    // Add user message to the chat
    var userMessage = document.createElement('div');
    userMessage.classList.add('user-message');
    userMessage.textContent = answer;
    chatContent.appendChild(userMessage);

    // Simulate bot's predefined answer
    var predefinedAnswers = {
      "What can you offer for us?": "I can help customers find products, explain store policies, assist with the checkout process, and provide support for any issues with orders or purchases.",
      "Do you support GCash payments?": "Yes, we support GCash payment method.",
      "Do you support Cash on Delivery payments?": "Yes, we support Cash on Delivery payment method.",
      "How long it takes to deliver the items?": "Delivery times depend on the origin and destination. For example, a package from Metro Manila to Metro Manila can take 3 days, while a package from Metro Manila to Visayas and Mindanao can take 5 days",
      "How can I contact customer support?": "You can reach our customer support team at (0909) 530-0049 for assistance. "
    };

    setTimeout(() => {
      var botResponse = document.createElement('div');
      botResponse.classList.add('bot-message');
      botResponse.textContent = predefinedAnswers[answer] ||  "For inquiries or more information, contact our customer service team at 09095300049 (TNT). We're available to assist you anytime!";
      chatContent.appendChild(botResponse);

      // Show the "Back to Questions" button
      document.getElementById('back-to-questions').style.display = 'block';

      // Scroll to the latest message
      chatContent.scrollTop = chatContent.scrollHeight;
    }, 500); // Simulated delay for bot response
    document.getElementById('pre-questions').style.display = 'none';
  }

  function sendMessage() {
    var message = document.getElementById('chat-input').value;
    if (message.trim() !== '') {
        var chatContent = document.getElementById('chat-content');
        
        // Add user message to the chat
        var userMessage = document.createElement('div');
        userMessage.classList.add('user-message');
        userMessage.textContent = message;
        chatContent.appendChild(userMessage);

        // Clear input field
        document.getElementById('chat-input').value = '';

        // Predefined answers
        var predefinedAnswers = {
            "What can you offer for us?": "I can help customers find products, explain store policies, assist with the checkout process, and provide support for any issues with orders or purchases.",
            "Do you support GCash payments?": "Yes, we support GCash payment method.",
            "Do you support Cash on Delivery payments?": "Yes, we support Cash on Delivery payment method.",
            "How long it takes to deliver the items?": "Delivery times depend on the origin and destination. For example, a package from Metro Manila to Metro Manila can take 3 days, while a package from Metro Manila to Visayas can take 5 days.",
            "How can I contact customer support?": "You can reach our customer support team at (0909) 530-0049 for assistance."
        };

        // Simulated bot's response
        var botResponse = document.createElement('div');
        botResponse.classList.add('bot-message');

        // Check if the message matches a predefined question
        if (predefinedAnswers[message]) {
            botResponse.textContent = predefinedAnswers[message];
        } else {
            // Default response for questions outside the predefined ones
            botResponse.textContent = "For inquiries or more information, contact our customer service team at 09095300049 (TNT). We're available to assist you anytime!";
        }
        
        // Add bot response to the chat
        chatContent.appendChild(botResponse);

        // Scroll to the latest message
        chatContent.scrollTop = chatContent.scrollHeight;
    }
}


  function showPreQuestions() {
    document.getElementById('pre-questions').style.display = 'flex';
    document.getElementById('back-to-questions').style.display = 'none';
    var chatContent = document.getElementById('chat-content');
    chatContent.scrollTop = chatContent.scrollHeight;
  }

  function closeChat() {
    var chatWindow = document.getElementById('chat-window');
    chatWindow.style.bottom = '-450px';  // Animate down
    setTimeout(function () {
      chatWindow.style.display = 'none';  // Hide it after animation
    }, 500);  // Wait for the animation to finish
  }
</script>

<style>
  #back-to-questions {
  display: none;
  padding: 10px;
  text-align: center;
  position: relative;
  z-index: 999; /* Ensure it's on top */
}

.pre-question {
  background-color: #007bff;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-size: 16px;
  position: relative;
  z-index: 1; /* To ensure buttons stay behind the back-to-questions button */
}

.pre-question:hover {
  background-color: #0056b3;
}

.user-message {
  background-color: #007bff;
  color: white;
  padding: 12px;
  margin-bottom: 10px;
  border-radius: 10px;
  max-width: 80%;
  align-self: flex-end;
  font-size: 16px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1; /* Keep messages below the back-to-questions button */
}

.bot-message {
  background-color: #f1f1f1;
  color: #333;
  padding: 12px;
  margin-bottom: 10px;
  border-radius: 10px;
  max-width: 80%;
  align-self: flex-start;
  font-size: 16px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 1; /* Keep bot responses below the back-to-questions button */
}

#chat-content {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.user-message {
  background-color: #007bff;
  color: white;
  padding: 12px;
  margin-bottom: 10px;
  border-radius: 10px;
  max-width: 80%;
  align-self: flex-end; /* Right align the user's message */
  font-size: 16px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.bot-message {
  background-color: #f1f1f1;
  color: #333;
  padding: 12px;
  margin-bottom: 10px;
  border-radius: 10px;
  max-width: 80%;
  align-self: flex-start; /* Left align the bot's message */
  font-size: 16px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}




</style>
