### Task
Parse http://ananaska.com/vse-novosti/
Each article must be saved to DB incl. H1, text and all content

### Algo
1. Go to catalog page where all links to articles located
2. Save all links to articles
3. Get each article link and get article body and H1
4. Save article to DB

### How to use
`php parse.php catalog` - get articles list
`php parse.php article` - get all articles content
