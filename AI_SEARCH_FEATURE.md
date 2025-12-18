# AI-Powered Job Search Feature Documentation

## Overview
The AI-powered job search feature allows users to describe their ideal job in natural language, and the system will use AI to match and display the most relevant job postings from your database.

## Features

### 1. **Natural Language Job Search**
   - Users can type a description of their desired job instead of using traditional filters
   - Example: "I'm looking for a senior developer role in Python with machine learning experience in Paris"

### 2. **AI Matching Algorithm**
   - Uses OpenAI's GPT-3.5 Turbo model for semantic understanding
   - Fallback to keyword-based search if API is not configured or unavailable
   - Filters results based on relevance to the user's description

### 3. **Instant Feedback**
   - Real-time search with loading indicators
   - Success/error notifications
   - Results count displayed to the user

## Setup Instructions

### Step 1: Configure OpenAI API Key

1. Get your OpenAI API key from https://platform.openai.com/account/api-keys
2. Open `config/ai.php`
3. Replace `'sk-your-openai-api-key-here'` with your actual API key:

```php
define('OPENAI_API_KEY', 'sk-your-actual-api-key-here');
```

### Step 2: Ensure Guzzle HTTP Client is Installed

The Guzzle HTTP client is already installed via Composer. If not, run:

```bash
composer require guzzlehttp/guzzle
```

### Step 3: Test the Feature

1. Navigate to the "Offres" (Job Offers) page
2. Scroll to the "Recherche Intelligente avec IA" section
3. Type a job description
4. Click "Rechercher avec IA" (Search with AI)
5. Review the matching job results

## How It Works

### Frontend (offres.php)
- **AI Search Form**: User input field with submit button
- **AJAX Handler**: Sends search query to the backend
- **Results Display**: Shows matching jobs in a grid format
- **Notifications**: Provides user feedback on search status

### Backend (JobController.php)
- **searchJobsWithAI()**: Main method that processes the search
- **getAIMatchedJobIds()**: Calls OpenAI API or uses fallback keyword search
- **parseAIResponse()**: Extracts job IDs from AI response
- **basicKeywordSearch()**: Fallback search using keyword matching

## API Configuration

### OpenAI API Settings (config/ai.php)

```php
define('OPENAI_API_KEY', 'your-key-here');      // Your OpenAI API key
define('OPENAI_MODEL', 'gpt-3.5-turbo');        // Model to use
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'); // API endpoint
```

### Request/Response Flow

1. **User Input**: "Je cherche un développeur web frontend avec React"
2. **Backend Processing**:
   - Loads all jobs from database
   - Prepares job summaries (title, company, location, description)
   - Sends to OpenAI API with the user's query
3. **AI Response**: OpenAI returns job numbers that match (e.g., "1,3,5")
4. **Filtering**: System filters jobs based on matched IDs
5. **Frontend Display**: Results rendered as job cards

## Fallback Mechanism

If OpenAI API is not configured or unavailable:
- System automatically falls back to keyword-based search
- Searches across job title, description, company, and location
- Matches based on word presence in job fields

## Error Handling

The system handles the following scenarios:

1. **No API Key**: Uses keyword search fallback
2. **API Error**: Catches exception and uses keyword search
3. **Empty Query**: Shows warning notification
4. **No Results**: Displays "No jobs found" message
5. **Network Error**: Shows error notification with retry option

## Search Examples

### Example 1: Full-Stack Developer
```
Je cherche un poste de développeur full stack avec expérience en JavaScript, 
React, Node.js et MongoDB. Je préfère une entreprise en startup mode basée à Paris.
```

### Example 2: Data Scientist
```
Offre de poste dans la data science avec focus sur Python, machine learning et 
analyse de big data. Télétravail possible.
```

### Example 3: Product Manager
```
Je recherche un rôle de Product Manager senior dans une entreprise technologique 
avec au moins 5 ans d'expérience en gestion de produits.
```

## Performance Considerations

- **API Calls**: Each search makes one API call (costs tokens)
- **Timeout**: 10-second timeout to prevent hanging
- **Token Usage**: ~100 max tokens per request
- **Rate Limiting**: Be aware of OpenAI's rate limits

## Security Notes

1. **API Key Protection**: Never commit your API key to version control
2. **Input Validation**: User input is validated and escaped
3. **SQL Injection**: Protected via parameterized queries
4. **XSS Protection**: HTML special characters are escaped

## Customization

### Change Search Prompt
Edit the prompt in `searchJobsWithAI()` method to adjust AI behavior:

```php
$prompt = "Your custom prompt here...";
```

### Adjust Temperature
Modify the temperature setting for more/less creative results (0.0-1.0):
- Lower (0.3): More focused, consistent results
- Higher (0.7+): More creative, varied results

### Add More Context
Include additional job fields in the prompt for better matching:
```php
$jobSummaries = array_map(function($job, $index) {
    return ($index + 1) . ". Title: " . $job['title'] . 
           " | Skills: " . $job['required_skills'] . // Add more fields
           " | Benefits: " . $job['benefits'];
}, $jobs, array_keys($jobs));
```

## Troubleshooting

### Issue: "No jobs found" even with relevant jobs
- Check if jobs are properly saved in database
- Verify job descriptions have enough detail
- Try a simpler search query

### Issue: API errors
- Verify API key is correct
- Check if you have sufficient API credits
- Review OpenAI usage limits

### Issue: Slow search
- Consider adding pagination
- Optimize database queries
- Monitor API response times

## Future Enhancements

1. **Advanced Filtering**: Add filters for salary range, contract type, location
2. **Search History**: Save user searches for recommendations
3. **Favorites**: Allow users to save favorite jobs
4. **Notifications**: Email alerts for matching jobs
5. **Analytics**: Track search patterns and user preferences

## Support

For issues or questions:
1. Check the error notification messages
2. Review browser console for JavaScript errors
3. Check PHP error logs
4. Verify OpenAI API status at https://status.openai.com/
