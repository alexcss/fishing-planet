import React, { useState, useEffect } from 'react'

interface MobileSearchButtonProps {
  value: string
  onChange: (value: string) => void
}

const MobileSearchButton: React.FC<MobileSearchButtonProps> = ({ value, onChange }) => {
  const [localValue, setLocalValue] = useState(value ?? '')

  useEffect(() => {
    setLocalValue(value ?? '')
  }, [value])

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    onChange(localValue)
  }

  return (
    <form
      onSubmit={handleSubmit}
      className="fp-btn-corners flex h-64 w-full items-center justify-between gap-12 bg-transparent px-20 pr-0 pl-16 focus-within:border-white/20"
    >
      <span className="flex-1">
        <input
          type="text"
          value={localValue}
          onChange={(e) => setLocalValue(e.target.value)}
          placeholder="Search"
          minLength={3}
          maxLength={50}
          className="fp-captital-title-sm w-full placeholder:text-white/50 focus:outline-none"
        />
      </span>
      {localValue && (
        <button
          type="button"
          aria-label="Clear search"
          className="-mr-10 shrink-0 py-16 text-white/50 hover:text-white"
          onClick={() => {
            setLocalValue('')
            onChange('')
          }}
        >
          <svg className="h-20 w-20">
            <use href="#icon-close" />
          </svg>
        </button>
      )}
      <button type="submit" aria-label="Search" className="shrink-0 p-16">
        <svg className="h-24 w-24 text-white">
          <use href="#icon-search" />
        </svg>
      </button>
    </form>
  )
}

export default MobileSearchButton
