import React from 'react'

const MobileSearchButton: React.FC = () => (
  <button className="fp-btn-corners flex h-64 w-full items-center justify-between bg-transparent px-20" aria-label="Search">
    <span className="fp-captital-title">Search</span>
    <svg className="h-24 w-24 text-white">
      <use href="#icon-search" />
    </svg>
  </button>
)

export default MobileSearchButton
