import React from 'react'
import DlcCard from './DlcCard'
import type { DlcPost } from './types'

interface DlcGridProps {
  posts: DlcPost[]
}

const DlcGrid: React.FC<DlcGridProps> = ({ posts }) => {
  return (
    <div className="space-y-32">
      {posts.map((dlc) => (
        <DlcCard key={dlc.id} dlc={dlc} />
      ))}
    </div>
  )
}

export default DlcGrid
