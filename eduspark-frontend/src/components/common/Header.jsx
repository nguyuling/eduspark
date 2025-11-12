import React from 'react';
import { Link } from 'react-router-dom';

const Header = () => {
  return (
    <nav className="navbar navbar-expand-lg navbar-dark bg-primary">
      <div className="container">
        <Link className="navbar-brand" to="/">
          🎮 EduSpark - SPM Computer Science
        </Link>
        <div className="navbar-nav">
          <Link className="nav-link" to="/">Home</Link>
        </div>
      </div>
    </nav>
  );
};

export default Header;