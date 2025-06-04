# PROMPT

## Context
You are an expert in music analysis. I will provide you with:
1. A list of reference songs (title + main artist)
2. A catalog of candidate songs with their IDs (id + title + main artist)

## Task
Select the songs from the catalog that are most similar to the reference songs, based on:
- Musical genre
- Artist style
- Era/period
- General mood/vibe
- Musical influences

## Response Format
Respond ONLY with a valid JSON containing the IDs of selected songs:
```json
["song_id_1", "song_id_2", "song_id_3", ...]
```

## Input Data

### Reference Songs:
{{ references }}

### Candidate Songs Catalog:
{{ catalog }}

## Important Instructions
- Select 10 songs maximum or fewer if fewer are available
- Prioritize diversity while maintaining coherence
- Return ONLY the JSON, no other text
- Ensure all IDs exist in the provided catalog

