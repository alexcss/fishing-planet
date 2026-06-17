import React, { useState, useRef, useEffect } from 'react'
import type { FilterOption } from './types'

interface FilterDropdownProps {
  label: string
  options: FilterOption[]
  selected: string
  onSelect: (value: string) => void
  searchPlaceholder?: string
  showSearch?: boolean
}

const FilterDropdown: React.FC<FilterDropdownProps> = ({
  label,
  options,
  selected,
  onSelect,
  searchPlaceholder = 'Search...',
  showSearch = false,
}) => {
  const [isOpen, setIsOpen] = useState(false)
  const [searchQuery, setSearchQuery] = useState('')
  const dropdownRef = useRef<HTMLDivElement>(null)

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false)
      }
    }

    document.addEventListener('mousedown', handleClickOutside)
    return () => document.removeEventListener('mousedown', handleClickOutside)
  }, [])

  const filteredOptions = searchQuery ? options.filter((opt) => opt.name.toLowerCase().includes(searchQuery.toLowerCase())) : options

  const selectedOption = options.find((opt) => opt.slug === selected)

  return (
    <div ref={dropdownRef} className="relative">
      {/* Dropdown Button */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="hover:border-accent flex h-64 w-240 shrink-0 items-center justify-between gap-12 border border-white/15 bg-black px-24 py-20 text-left transition-colors"
      >
        <span
          className="fp-captital-title min-w-0 truncate"
          dangerouslySetInnerHTML={{ __html: selectedOption ? selectedOption.name : label }}
        ></span>
        <svg className={`h-24 w-24 shrink-0 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
          <use href="#icon-arrow-down" />
        </svg>
      </button>

      {/* Dropdown Menu */}
      {isOpen && (
        <div className="absolute top-full right-0 left-0 z-50 mt-4 border border-white/15 bg-black shadow-2xl">
          {/* Search Input */}
          {showSearch && (
            <div className="border-b border-white/15 px-20 py-16">
              <div className="flex items-center gap-12">
                <svg className="h-24 w-24 text-white/50">
                  <use href="#icon-search" />
                </svg>
                <span className="font-heading text-24 text-white/50 uppercase">{searchPlaceholder}</span>
              </div>
            </div>
          )}

          {/* Options List */}
          <div className="fp-scrollbar-thin max-h-320 overflow-y-auto pb-20">
            {/* All Option */}
            <button
              onClick={() => {
                onSelect('')
                setIsOpen(false)
              }}
              className="w-full px-20 transition-colors hover:bg-white/5"
            >
              <span className="flex items-center justify-between border-b border-white/15 py-16">
                <span className={`font-heading text-24 leading-none uppercase ${selected === '' ? 'text-white' : 'text-white/50'}`}>All</span>
                {selected === '' && (
                  <svg className="h-24 w-24 text-white">
                    <use href="#icon-check" />
                  </svg>
                )}
              </span>
            </button>

            {/* Filtered Options */}
            {filteredOptions.map((option, index) => (
              <React.Fragment key={option.slug}>
                <button
                  onClick={() => {
                    onSelect(option.slug)
                    setIsOpen(false)
                  }}
                  className="w-full px-20 transition-colors hover:bg-white/5"
                >
                  <span className="flex items-center justify-between border-b border-white/15 py-16">
                    <span
                      className={`font-heading text-24 text-left leading-none uppercase ${selected === option.slug ? 'text-white' : 'text-white/50'}`}
                      dangerouslySetInnerHTML={{ __html: option.name }}
                    ></span>
                    {selected === option.slug && (
                      <svg className="h-24 w-24 text-white">
                        <use href="#icon-check" />
                      </svg>
                    )}
                  </span>
                </button>
              </React.Fragment>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}

export default FilterDropdown
