# AI Feature — Simple Explanation

This project includes a simple AI-powered job search feature that uses OpenAI to perform semantic matching between a user's natural-language query and the job postings stored in the database. This README explains what is implemented, where the code lives, how to configure it, and how to use/test it.

**Overview**
- **What it does:** Users can enter a natural-language description of the job they want. The backend prepares a short list of job summaries and asks OpenAI to return the job numbers that best match the query. The system converts those numbers to job IDs and returns the matching job records.
- **Fallback:** If no API key is configured or there is an API error, the system falls back to a basic keyword-based search.

**Where to find the code**
- **AI config:** `config/ai.php` — contains `OPENAI_API_KEY`, `OPENAI_MODEL`, and `OPENAI_API_URL`.
- **Main integration:** `controller/JobController.php`
  - `searchJobsWithAI($query)` — top-level method used by the frontend to run an AI search.
  - `getAIMatchedJobIds($userQuery, $jobs)` — prepares job summaries, calls OpenAI, and returns matched job IDs.
  - `parseAIResponse($response)` — extracts numbers from the AI response.
  - `basicKeywordSearch($query, $jobs)` — fallback keyword search across chosen job attributes.
- **Documentation:** `AI_SEARCH_FEATURE.md` — longer doc with examples and notes.

**How it works (short)**
1. Frontend sends the user's natural-language query to the backend endpoint (an AJAX call to the job controller with action `searchWithAI` or `aiSearch`).
2. Backend loads all jobs (filtered attributes), builds a numbered list of job summaries, and constructs a prompt for OpenAI.
3. If an API key is present, the controller sends a JSON request to OpenAI (via Guzzle) using `OPENAI_MODEL` and `OPENAI_API_URL`.
4. OpenAI is instructed to return ONLY job numbers (comma-separated) that match the query.
5. The controller maps returned numbers to actual job IDs and returns the matching job objects to the frontend.
6. If the OpenAI call fails or the API key is missing, `basicKeywordSearch()` is used instead.

**Configuration (what you must set)**
- Edit `config/ai.php` and set your real API key:

```
define('OPENAI_API_KEY', 'sk-...replace-with-your-key...');
define('OPENAI_MODEL', 'gpt-4o');
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
```

- Do NOT commit your API key to source control.
- Recommended settings used in the code: `max_tokens` ~50, `temperature` 0.2, `timeout` 30s.

**Dependencies**
- `guzzlehttp/guzzle` — used for HTTP calls to the OpenAI API (already in `vendor/`).
- PHP with `json` and `curl` support; PDO for DB access.

**How to call/test the AI search (quick)**
- From the frontend: the project expects an AJAX request to the JobController with `action=searchWithAI` or `action=aiSearch` and `query`.
- Example curl (replace host and query):

```powershell
# GET example
Invoke-WebRequest -Uri "http://localhost/projectphp/controller/JobController.php?action=searchWithAI&query=$("Developer with React in Paris" -replace ' ', '%20')" -UseBasicParsing

# POST example using curl (Windows PowerShell with curl alias)
curl -X POST "http://localhost/projectphp/controller/JobController.php?action=searchWithAI" -d "query=Developer with React in Paris"
```

**Security & Best Practices**
- Keep the API key out of the repository and use environment-specific config where possible.
- Limit timeout and token usage to control costs.
- Validate user input and escape output when rendering.

**Troubleshooting**
- If you get no results:
  - Confirm jobs exist in the database.
  - Try the same query with the fallback keyword search (the app does this automatically if the API fails).
- If you get API errors:
  - Check `config/ai.php` for the correct key.
  - Check OpenAI account usage/quotas.
  - Inspect server logs for Guzzle exceptions (connection, client, or other errors).

**Where to customize**
- Prompt text: edit the prompt built in `getAIMatchedJobIds()` to change instructions and context sent to the model.
- Model & parameters: change `OPENAI_MODEL`, `temperature`, and `max_tokens` where the Guzzle request is built.
- Job fields included in summaries: modify `searchJobsWithAI()` job summary composition to add skills, requirements, benefits, etc., to improve matching.

**Notes**
- The project already contains a human-readable `AI_SEARCH_FEATURE.md` with longer explanations and examples — this README is a compact, actionable summary pointing you to the implementation spots.

If you want, I can also:
- Add a small admin-only page to test queries and view the raw AI response.
- Replace the hard-coded `OPENAI_API_KEY` with an environment-variable-based loader and update `config/ai.php` accordingly.

---
File created: `README.md` (project root)
