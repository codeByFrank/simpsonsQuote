import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, tap, of, catchError, throwError } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  
  private loginUrl: string = 'http://localhost:8000/api/login';
  private quotesUrl: string = 'http://localhost:8000/api/quotes';
  private logoutUrl: string = 'http://localhost:8000/api/logout';

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  /**
   * Sends a login request with the provided username and password.
   * If successful, stores the returned token in localStorage. 
   * @param username 
   * @param password 
   * @returns 
   */
  login(username: string, password: string): Observable<any> {
    const body = { username, password };
    return this.http.post<any>(this.loginUrl, body).pipe(
      tap(response => {
        if (response.token) {
          localStorage.setItem('token', response.token);
        }
      })
    );
  }

  /**
   * Sends a logout request to the backend.
   * On success, removes the token from localStorage and navigates to the login page.
   * If no token is present, navigates directly to the login page.
   */
  logout(): Observable<any> {
    const token = localStorage.getItem('token');
    if (!token) {
      this.router.navigate(['/login']);
      return of(null);
    }
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);
    return this.http.post<any>(this.logoutUrl, {}, { headers }).pipe(
      tap(() => {
        localStorage.removeItem('token');
        this.router.navigate(['/login']);
      })
    );
  }

   /**
   * Retrieves quotes from the backend.
   * Adds the JWT token to the request headers for authentication.
   * If the token is invalid or missing, navigates to the login page.
   */
   getQuotes(): Observable<any[]> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);
    
    return this.http.get<any[]>(this.quotesUrl, { headers }).pipe(
      catchError(err => {
        if (err.status === 401 || err.status === 403) {
          // If unauthorized, navigate to login page
          this.router.navigate(['/login']);
        }
        return throwError(err);
      })
    );
  }
}
