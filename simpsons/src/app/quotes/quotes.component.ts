import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../services/auth.service';
import { MatCardModule } from '@angular/material/card';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatDividerModule } from '@angular/material/divider';

@Component({
  selector: 'app-quotes',
  standalone: true,
  imports: [
    CommonModule,
    MatDividerModule,
    MatCardModule,
    MatProgressSpinnerModule
  ],
  templateUrl: './quotes.component.html',
  styleUrl: './quotes.component.scss'
})
export class QuotesComponent implements OnInit {
  quotes: any[] = [];

  constructor(
    private authService: AuthService
  ) { }

  ngOnInit(): void {
    this.authService.getQuotes().subscribe({
      next: (data) => {
        this.quotes = data;
      },
      error: (err) => {
        console.error('Error fetching quotes:', err);
      }
    });
  }
}