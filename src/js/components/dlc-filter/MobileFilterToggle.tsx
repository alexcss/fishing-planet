import React from 'react'

interface MobileFilterToggleProps {
  isOpen: boolean
  activeFilterCount: number
  onToggle: () => void
}

const MobileFilterToggle: React.FC<MobileFilterToggleProps> = ({ isOpen, activeFilterCount, onToggle }) => (
  <button
    onClick={onToggle}
    className={`flex w-full items-center justify-between px-24 py-22 ${!isOpen ? 'text-white' : 'text-white/50'}`}
  >
    <span className="fp-captital-title">Filters</span>
    <div className="flex items-center gap-16">
      {activeFilterCount > 0 && <span className="font-heading text-24/none">({activeFilterCount})</span>}
      <svg className={`h-24 w-24 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
        <use href="#icon-arrow-down" />
      </svg>
    </div>
  </button>
)

export default MobileFilterToggle
