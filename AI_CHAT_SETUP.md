# AI Doctor Chat Setup Instructions

## Overview
The AI Doctor chat feature uses ChatGPT-4o API to provide medical consultation with structured diagnosis output. The system follows a specific prompt structure to ensure consistent medical advice and urgency level assessment.

## Prerequisites
- OpenAI API key with access to GPT-4o
- Laravel application with authentication system
- MySQL database

## Configuration

### 1. OpenAI API Key Setup
Add your OpenAI API key to your `.env` file:
```env
OPENAI_API_KEY=your_openai_api_key_here
```

### 2. Database Tables
The following tables are automatically created by migrations:
- `chat_sessions` - Stores chat sessions for each user
- `chat_messages` - Stores individual messages in each session

## Features Implemented

### 1. **AI Doctor Chat Interface**
- Real-time chat interface matching the provided design
- AI Doctor avatar with "オンライン" status
- User and AI message bubbles with timestamps
- Responsive design with Tailwind CSS

### 2. **ChatGPT-4o Integration**
- Uses GPT-4o model for medical consultation
- Streaming responses for real-time interaction
- Structured function calling for diagnosis output
- Japanese language support throughout

### 3. **Structured Diagnosis System**
The AI follows a specific prompt structure that returns:

**For Incomplete Symptoms:**
```json
{
  "missing_questions": ["必要な質問を日本語で"]
}
```

**For Complete Symptoms:**
```json
{
  "diagnosis": [
    {
      "disease": "病名を日本語で",
      "probability": 数値 (0〜100),
      "urgency_level": "レベル1" | "レベル2" | "レベル3",
      "description": "簡単な病状説明（日本語で）"
    }
  ],
  "overall_urgency_level": "レベル1" | "レベル2" | "レベル3",
  "advice": "診断結果に基づいた日本語でのアドバイス（2〜3文）"
}
```

### 4. **Urgency Level Classification**
- **レベル3**: 緊急 - すぐに救急車を呼ぶか、救急外来を受診
- **レベル2**: 要注意 - できるだけ早く医療機関を受診
- **レベル1**: 軽症 - 様子を見て、必要に応じて医療機関を受診

### 5. **Chat Session Management**
- Automatic session creation for new conversations
- Chat history persistence in database
- Session linking for continuous conversations
- User authentication required

## Usage Flow

1. **User clicks "問診を開始→"** on dashboard
2. **Chat interface opens** with AI Doctor greeting
3. **User describes symptoms** in Japanese
4. **AI analyzes symptoms** and either:
   - Asks follow-up questions if information is insufficient
   - Provides structured diagnosis with urgency level if sufficient
5. **Chat continues** until diagnosis is complete
6. **Results are stored** in database for future reference

## API Endpoints

- `GET /chat` - Show chat interface
- `POST /chat/send` - Send message to AI Doctor
- `GET /chat/history/{sessionId}` - Get chat history

## Security Features

- CSRF protection on all forms
- User authentication required
- Input validation and sanitization
- Rate limiting (can be added)
- Secure API key storage

## Error Handling

- Network error handling
- API error responses
- User-friendly error messages in Japanese
- Logging for debugging

## Customization

### Modifying the System Prompt
Edit the `getSystemPrompt()` method in `ChatController.php` to customize:
- Medical knowledge base
- Response format
- Urgency level criteria
- Language preferences

### Adding New Functions
Extend the `getFunctions()` method to add new AI capabilities:
- Symptom tracking
- Medication recommendations
- Appointment scheduling
- Emergency contact information

## Testing

1. **Start the development server:**
   ```bash
   php artisan serve
   ```

2. **Login with admin account:**
   - Email: admin@gmail.com
   - Password: admin123

3. **Navigate to dashboard and click "問診を開始→"**

4. **Test with sample symptoms:**
   - "子どもが発熱しました。どうすればいいですか？"
   - "頭痛が続いています"
   - "腹痛があります"

## Troubleshooting

### Common Issues

1. **API Key Error**
   - Verify OPENAI_API_KEY is set in .env
   - Check API key permissions and billing

2. **Database Errors**
   - Run `php artisan migrate` to create tables
   - Check database connection settings

3. **Chat Not Working**
   - Check browser console for JavaScript errors
   - Verify CSRF token is present
   - Check network connectivity

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

Check Laravel logs in `storage/logs/laravel.log` for detailed error information.

## Performance Considerations

- Implement caching for frequent responses
- Add rate limiting for API calls
- Consider using queues for long-running requests
- Monitor API usage and costs

## Future Enhancements

- Voice input/output
- Image upload for visual symptoms
- Integration with medical databases
- Multi-language support
- Mobile app integration
- Emergency contact integration 