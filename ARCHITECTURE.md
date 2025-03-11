### Angular Frontend (Client):

- Sends HTTP requests to the backend (e.g., for login or fetching quotes).
  After a successful login, a JWT token is stored in the browser (e.g., in localStorage) and included in subsequent requests.
  Always receives 5 quotes from the backend (1 fresh quote + 4 from the database).
  Laravel Backend:

- The API & JWT Auth part (B) validates the token on each request (e.g., via auth:api).
  Delegates the quote logic to the Quote Service (D).
  Quote Service:

- Requests a fresh quote from the Simpsons Quote API (E).
  Retrieves the 4 old quotes from the database (C).
  Deletes the oldest quote and inserts the new one so that exactly 4 quotes remain in the database.
  Returns a total of 5 quotes (1 fresh + 4 from the database) to the backend (B).
  MySQL Database:

- Stores the latest 4 quotes.
  Provides the 4 old quotes to the Quote Service and adds the freshly fetched quote while removing the oldest one.
  External Simpsons Quote API:

- Returns a fresh quote that is fetched by the Quote Service and then inserted into the database.
  Direct access from the frontend is prevented because the backend filters sensitive data.
