import { Component } from '@angular/core';
import { AuthService } from '../services/auth.service';
import { MatButton } from '@angular/material/button';

@Component({
  selector: 'app-logout',
  standalone: true,
  imports: [MatButton],
  templateUrl: './logout.component.html',
  styleUrl: './logout.component.scss'
})
export class LogoutComponent {

  constructor(
     private authService: AuthService
  ) {}

  
  logout(): void {
    this.authService.logout().subscribe();
  }
}