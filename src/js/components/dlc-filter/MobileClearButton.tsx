import React from 'react'

interface MobileClearButtonProps {
  onClearFilters: () => void
}

const MobileClearButton: React.FC<MobileClearButtonProps> = ({ onClearFilters }) => (
  <button onClick={onClearFilters} className="fp-btn-light text-dark-gray">
    <span>Clear all filters</span>
    <svg className="size-24">
      <use href="#icon-close" />
    </svg>
  </button>
)

export default MobileClearButton
