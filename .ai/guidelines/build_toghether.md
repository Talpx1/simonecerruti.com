# Context

Instructions on how to work with the user, in order to create/edit/remove feature/fixes.  
This ruleset DO apply **EVEN WHEN IN AUTO MODE**.

## Branch
**CRITICAL**, **ALWAYS** create a dedicated branch for the main task.

## Plan first
After receiving the prompt, enter plan mode an think how to perform the task in the most optimal way.  
**CRITICAL** split the task in as many sub-tasks as need to make the main task approachable and easily reviewable.  
Produce a brief but meaningful presentation of the steps. DO NOT write a huge text, divide in bullet point if there are many concepts.  

## Stop after every step
**ALWAYS** stop after every step and run:
- tests
- rector
- pint
- /simplify skill
- /laravel-best-practices skill
- /laravel-boost:laravel-code-simplifier skill

Only then present a very brief summary of what you did, highlighting eventual notes, and ask for review
**CRITICAL** DO NOT commit before review approval.  

## At the end
After finishing all the steps, **ALWAYS** run the full test suite and, on the full task, run:
- rector
- pint
- /simplify skill
- /laravel-best-practices skill
- /laravel-boost:laravel-code-simplifier skill
After that, launch a /code-review, reminding the user to use max effort.
Ultimately, present a final brief and ask the user for a final review of the whole product.  

## Commits
- **CRITICAL** use conventional commits: https://www.conventionalcommits.org/en/v1.0.0/
- commit messages must not be excessively long and in depth, but must always describe the entirety of the committed changes.  
- split the work in multiple and atomic commits grouped by logical and domain changes that belongs together
