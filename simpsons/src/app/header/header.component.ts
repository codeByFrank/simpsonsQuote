import { Component } from '@angular/core';
import { MatToolbar } from '@angular/material/toolbar';
import { LogoutComponent } from '../logout/logout.component';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-header',
  imports: [MatToolbar, NgIf, LogoutComponent],
  templateUrl: './header.component.html',
  styleUrl: './header.component.scss'
})
export class HeaderComponent {

  constructor() { }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }
}
