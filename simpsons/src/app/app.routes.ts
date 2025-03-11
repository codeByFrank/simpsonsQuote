import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { QuotesComponent } from './quotes/quotes.component';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'quotes', component: QuotesComponent },
  { path: '**', redirectTo: 'login' }
];
